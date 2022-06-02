<?php

declare(strict_types=1);

namespace App\Api\Controller;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Annotation\Controller as Api;
use SixtyEightPublishers\HealthCheck\HealthCheckerInterface;

/**
 * @Api\Path("/health-check")
 */
final class HealthCheckController extends AbstractController
{
	private bool $debugMode;

	private HealthCheckerInterface $healthChecker;

	/**
	 * @param bool                                                     $debugMode
	 * @param \SixtyEightPublishers\HealthCheck\HealthCheckerInterface $healthChecker
	 */
	public function __construct(bool $debugMode, HealthCheckerInterface $healthChecker)
	{
		$this->debugMode = $debugMode;
		$this->healthChecker = $healthChecker;
	}

	/**
	 * @Api\Path("/")
	 * @Api\Method("GET")
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return ApiResponse
	 */
	public function index(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$result = $this->healthChecker->check([], $this->debugMode ? HealthCheckerInterface::ARRAY_EXPORT_MODE_FULL : HealthCheckerInterface::ARRAY_EXPORT_MODEL_SIMPLE);

		return $response
			->withStatus($result->isOk() ? ApiResponse::S200_OK : ApiResponse::S503_SERVICE_UNAVAILABLE)
			->writeJsonObject($result);
	}
}
