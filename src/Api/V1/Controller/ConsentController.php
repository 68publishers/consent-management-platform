<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use DomainException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\ReadModel\Project\ProjectView;
use Apitte\Core\Annotation\Controller as API;
use App\ReadModel\Project\GetProjectByCodeQuery;
use App\Domain\Consent\Command\StoreConsentCommand;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\ReadModel\ConsentSettings\GetConsentSettingsByProjectIdAndChecksumQuery;

/**
 * @API\Path("/consent")
 */
final class ConsentController extends AbstractV1Controller
{
	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 */
	public function __construct(CommandBusInterface $commandBus, QueryBusInterface $queryBus)
	{
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
	}

	/**
	 * @API\Path("/{project}/{userIdentifier}")
	 * @API\Method("PUT")
	 * @API\RequestParameters({
	 *      @API\RequestParameter(name="project", type="string", in="path", description="Project code"),
	 *      @API\RequestParameter(name="userIdentifier", type="string", in="path", description="Unique user identifier e.g. uuid, session id"),
	 * })
	 * @API\RequestBody(entity="App\Api\V1\RequestBody\PutConsentRequestBody", required=true)
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 */
	public function put(ApiRequest $request, ApiResponse $response): ApiResponse
	{
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

		try {
			$this->commandBus->dispatch(StoreConsentCommand::create(
				$projectView->id->toString(),
				$request->getParameter('userIdentifier'),
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
