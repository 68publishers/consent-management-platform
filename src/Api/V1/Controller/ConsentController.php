<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use Apitte\Core\Annotation\Controller as Api;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Api\V1\RequestBody\PutConsentRequestBody;
use App\Application\GlobalSettings\EnabledEnvironmentsResolver;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\Consent\Command\StoreConsentCommand;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\ConsentSettings\GetConsentSettingsByProjectIdAndChecksumQuery;
use App\ReadModel\Project\GetProjectByCodeQuery;
use App\ReadModel\Project\ProjectView;
use DomainException;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Symfony\Component\Lock\LockFactory;

/**
 * @Api\Path("/consent")
 */
final class ConsentController extends AbstractV1Controller
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly LockFactory $lockFactory,
        private readonly GlobalSettingsInterface $globalSettings,
    ) {}

    /**
     * @Api\Path("/{project}/{userIdentifier}")
     * @Api\Method("OPTIONS")
     * @Api\RequestParameters({
     *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
     *      @Api\RequestParameter(name="userIdentifier", type="string", in="path", description="Unique user identifier e.g. uuid, session id"),
     * })
     */
    public function options(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'PUT, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withStatus($response::S204_NO_CONTENT);
    }

    /**
     * @Api\Path("/{project}/{userIdentifier}")
     * @Api\Method("PUT")
     * @Api\RequestParameters({
     *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
     *      @Api\RequestParameter(name="userIdentifier", type="string", in="path", description="Unique user identifier e.g. uuid, session id"),
     * })
     * @Api\RequestBody(entity="App\Api\V1\RequestBody\PutConsentRequestBody", required=true)
     */
    public function put(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');

        /** @var PutConsentRequestBody $body */
        $body = $request->getEntity();
        $projectView = $this->queryBus->dispatch(GetProjectByCodeQuery::create($request->getParameter('project')));

        if (!$projectView instanceof ProjectView) {
            return $response->withStatus(ApiResponse::S422_UNPROCESSABLE_ENTITY)
                ->writeJsonBody([
                    'status' => 'error',
                    'data' => [
                        'code' => ApiResponse::S422_UNPROCESSABLE_ENTITY,
                        'error' => 'Project does not exist.',
                    ],
                ]);
        }

        if (null !== $body->environment) {
            $environments = EnabledEnvironmentsResolver::resolveProjectEnvironments(
                globalSettingsEnvironments: $this->globalSettings->environments(),
                projectEnvironments: $projectView->environments,
            );
            $environmentFound = false;

            foreach ($environments as $environment) {
                if ($environment->code === $body->environment) {
                    $environmentFound = true;

                    break;
                }
            }

            if (!$environmentFound) {
                return $response->withStatus(ApiResponse::S422_UNPROCESSABLE_ENTITY)
                    ->writeJsonBody([
                        'status' => 'error',
                        'data' => [
                            'code' => ApiResponse::S422_UNPROCESSABLE_ENTITY,
                            'error' => sprintf(
                                'Project does not have the "%s" environment.',
                                $body->environment,
                            ),
                        ],
                    ]);
            }
        }

        $userIdentifier = $request->getParameter('userIdentifier');
        $lock = $this->lockFactory->createLock(sprintf(
            'put-consent-%s-%s',
            $projectView->id,
            $userIdentifier,
        ));

        $lock->acquire(true);

        try {
            $this->commandBus->dispatch(StoreConsentCommand::create(
                projectId: $projectView->id->toString(),
                userIdentifier: $userIdentifier,
                settingsChecksum: $body->settingsChecksum,
                consents: $body->consents,
                attributes: $body->attributes,
                environment: $body->environment,
            ));
        } catch (DomainException $e) {
            return $response->withStatus(ApiResponse::S422_UNPROCESSABLE_ENTITY)
                ->writeJsonBody([
                    'status' => 'error',
                    'data' => [
                        'code' => ApiResponse::S422_UNPROCESSABLE_ENTITY,
                        'error' => $e->getMessage(),
                    ],
                ]);
        } finally {
            $lock->release();
        }

        $consentSettingsView = null !== $body->settingsChecksum ? $this->queryBus->dispatch(GetConsentSettingsByProjectIdAndChecksumQuery::create($projectView->id->toString(), $body->settingsChecksum)) : null;

        return $response->withStatus(ApiResponse::S200_OK)
            ->writeJsonBody([
                'status' => 'success',
                'data' => [
                    'consentSettingsExists' => $consentSettingsView instanceof ConsentSettingsView,
                ],
            ]);
    }
}
