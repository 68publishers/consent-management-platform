<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use DomainException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\ReadModel\Project\ProjectView;
use Apitte\Core\Annotation\Controller as API;
use App\ReadModel\Project\GetProjectByCodeQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Domain\ConsentSettings\Command\StoreConsentSettingsCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

/**
 * @API\Path("/consent-settings")
 */
final class ConsentSettingsController extends AbstractV1Controller
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
	 * @API\Path("/{project}/{checksum}")
	 * @API\Method("PUT")
	 * @API\RequestParameters({
	 *      @API\RequestParameter(name="project", type="string", in="path", description="Project code"),
	 *      @API\RequestParameter(name="checksum", type="string", in="path", description="Checksum of passed consent settings"),
	 * })
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 */
	public function put(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$projectView = $this->queryBus->dispatch(GetProjectByCodeQuery::create($request->getParameter('project')));

		if (!$projectView instanceof ProjectView) {
			return $response->withStatus(ApiResponse::S422_UNPROCESSABLE_ENTITY)
				->writeJsonBody([
					'error' => 'Project does not exist.',
				]);
		}

		try {
			$this->commandBus->dispatch(StoreConsentSettingsCommand::create(
				$projectView->id->toString(),
				$request->getParameter('checksum'),
				$request->getJsonBody(TRUE)
			));
		} catch (DomainException $e) {
			return $response->withStatus(ApiResponse::S422_UNPROCESSABLE_ENTITY)
				->writeJsonBody([
					'error' => $e->getMessage(),
				]);
		}

		return $response->withStatus(ApiResponse::S200_OK);
	}
}
