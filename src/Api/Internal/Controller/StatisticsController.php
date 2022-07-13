<?php

declare(strict_types=1);

namespace App\Api\Internal\Controller;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Nette\Security\User as NetteUser;
use Apitte\Core\Annotation\Controller as Api;

/**
 * @Api\Path("/statistics")
 */
final class StatisticsController extends AbstractInternalController
{
	public const ENDPOINT_PROJECTS = '/api/internal/statistics/projects';

	private NetteUser $user;

	/**
	 * @param \Nette\Security\User $user
	 */
	public function __construct(NetteUser $user)
	{
		$this->user = $user;
	}

	/**
	 * @Api\Path("/projects")
	 * @Api\Method("GET")
	 * @Api\RequestBody(entity="App\Api\Internal\RequestBody\GetProjectStatisticsRequestBody", required=true)
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 */
	public function getProjectStatistics(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$projects = (array) $request->getQueryParam('projects');

		return $response->withStatus(ApiResponse::S200_OK)
			->writeJsonBody([
				'status' => 'success',
				'data' => array_fill_keys($projects, [
					'allConsents' => [
						'value' => 2048,
						'percentageDiff' => 42,
					],
					'uniqueConsents' => [
						'value' => 1920,
						'percentageDiff' => -5,
					],
					'allPositive' => [
						'value' => 64,
						'percentageDiff' => 17,
					],
					'uniquePositive' => [
						'value' => 72,
						'percentageDiff' => 21,
					],
					'lastConsent' => [
						'value' => ($d = new \DateTimeImmutable('now'))->format(\DateTimeInterface::ATOM),
						'formattedValue' => $d->format('j.n.Y H:i:s'),
						'text' => 'a few seconds ago',
					],
					'providers' => [
						'value' => 14,
					],
					'cookies' => [
						'commonValue' => 82,
						'privateValue' => 12,
					],
				]),
			]);
	}
}
