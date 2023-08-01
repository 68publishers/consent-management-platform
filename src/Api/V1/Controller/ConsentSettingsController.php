<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use Apitte\Core\Annotation\Controller as Api;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Domain\ConsentSettings\Command\StoreConsentSettingsCommand;
use App\ReadModel\Project\GetProjectByCodeQuery;
use App\ReadModel\Project\ProjectView;
use DomainException;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Symfony\Component\Lock\LockFactory;

/**
 * @Api\Path("/consent-settings")
 */
final class ConsentSettingsController extends AbstractV1Controller
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly LockFactory $lockFactory,
    ) {}

    /**
     * @Api\Path("/{project}/{checksum}")
     * @Api\Method("OPTIONS")
     * @Api\RequestParameters({
     *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
     *      @Api\RequestParameter(name="checksum", type="string", in="path", description="Checksum of passed consent settings"),
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
     * @Api\Path("/{project}/{checksum}")
     * @Api\Method("PUT")
     * @Api\RequestParameters({
     *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
     *      @Api\RequestParameter(name="checksum", type="string", in="path", description="Checksum of passed consent settings"),
     * })
     */
    public function put(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
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

        $lock = $this->lockFactory->createLock(sprintf(
            'put-consent-settings-%s',
            $projectView->id,
        ));

        $lock->acquire(true);

        try {
            $this->commandBus->dispatch(StoreConsentSettingsCommand::create(
                $projectView->id->toString(),
                $request->getParameter('checksum'),
                $request->getJsonBody(),
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

        return $response->withStatus(ApiResponse::S200_OK)
            ->writeJsonBody([
                'status' => 'success',
                'data' => [],
            ]);
    }
}
