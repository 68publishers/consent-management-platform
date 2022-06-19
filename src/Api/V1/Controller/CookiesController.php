<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Annotation\Controller as Api;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Project\ProjectExistsQuery;
use App\ReadModel\Cookie\FindCookiesForApiQuery;
use App\Api\V1\RequestBody\GetCookiesRequestBody;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch;

/**
 * @Api\Path("/cookies")
 */
final class CookiesController extends AbstractV1Controller
{
	private QueryBusInterface $queryBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 */
	public function __construct(QueryBusInterface $queryBus)
	{
		$this->queryBus = $queryBus;
	}

	/**
	 * @Api\Path("/{project}")
	 * @Api\Method("OPTIONS")
	 * @Api\RequestParameters({
	 *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
	 * })
	 * @Api\RequestBody(entity="App\Api\V1\RequestBody\GetCookiesRequestBody", required=true)
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
			->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
			->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
			->withStatus($response::S204_NO_CONTENT);
	}

	/**
	 * @Api\Path("/{project}")
	 * @Api\Method("GET")
	 * @Api\RequestParameters({
	 *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
	 * })
	 * @Api\RequestBody(entity="App\Api\V1\RequestBody\GetCookiesRequestBody", required=true)
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 */
	public function get(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$projectId = $this->queryBus->dispatch(ProjectExistsQuery::byCode($request->getParameter('project')));

		$requestEntity = $request->getEntity();
		assert($requestEntity instanceof GetCookiesRequestBody);

		if (!$projectId instanceof ProjectId) {
			return $response->withStatus(ApiResponse::S422_UNPROCESSABLE_ENTITY)
				->writeJsonBody([
					'status' => 'error',
					'data' => [
						'code' => ApiResponse::S422_UNPROCESSABLE_ENTITY,
						'error' => 'Project does not exist.',
					],
				]);
		}

		$data = [];
		$query = FindCookiesForApiQuery::create($projectId->toString(), $requestEntity->locale)
			->withBatchSize(100);

		if (NULL !== $requestEntity->category) {
			$query = $query->withCategoryCodes((array) $requestEntity->category);
		}

		foreach ($this->queryBus->dispatch($query) as $batch) {
			assert($batch instanceof Batch);

			foreach ($batch->results() as $result) {
				$data[] = $result;
			}
		}

		return $response->withStatus(ApiResponse::S200_OK)
			->writeJsonBody([
				'status' => 'success',
				'data' => $data,
			]);
	}
}
