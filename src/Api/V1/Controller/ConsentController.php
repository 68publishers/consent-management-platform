<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use DomainException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\ReadModel\Project\ProjectView;
use Symfony\Component\Lock\LockFactory;
use Apitte\Core\Annotation\Controller as Api;
use App\ReadModel\Project\GetProjectByCodeQuery;
use App\Domain\Consent\Command\StoreConsentCommand;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\ReadModel\ConsentSettings\GetConsentSettingsByProjectIdAndChecksumQuery;

/**
 * @Api\Path("/consent")
 */
final class ConsentController extends AbstractV1Controller
{
	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	private LockFactory $lockFactory;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 * @param \Symfony\Component\Lock\LockFactory                              $lockFactory
	 */
	public function __construct(CommandBusInterface $commandBus, QueryBusInterface $queryBus, LockFactory $lockFactory)
	{
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
		$this->lockFactory = $lockFactory;
	}

	/**
	 * @Api\Path("/{project}/{userIdentifier}")
	 * @Api\Method("OPTIONS")
	 * @Api\RequestParameters({
	 *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
	 *      @Api\RequestParameter(name="userIdentifier", type="string", in="path", description="Unique user identifier e.g. uuid, session id"),
	 * })
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
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
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 */
	public function put(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$response = $response->withHeader('Access-Control-Allow-Origin', '*');

		/** @var \App\Api\V1\RequestBody\PutConsentRequestBody $body */
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

		$userIdentifier = $request->getParameter('userIdentifier');
		$lock = $this->lockFactory->createLock(sprintf(
			'put-consent-%s-%s',
			$projectView->id,
			$userIdentifier
		));

		$lock->acquire(TRUE);

		try {
			$this->commandBus->dispatch(StoreConsentCommand::create(
				$projectView->id->toString(),
				$userIdentifier,
				$body->settingsChecksum,
				$body->consents,
				$body->attributes
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

		$consentSettingsView = NULL !== $body->settingsChecksum ? $this->queryBus->dispatch(GetConsentSettingsByProjectIdAndChecksumQuery::create($projectView->id->toString(), $body->settingsChecksum)) : NULL;

		return $response->withStatus(ApiResponse::S200_OK)
			->writeJsonBody([
				'status' => 'success',
				'data' => [
					'consentSettingsExists' => $consentSettingsView instanceof ConsentSettingsView,
				],
			]);
	}
}
