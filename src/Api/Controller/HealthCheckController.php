<?php

declare(strict_types=1);

namespace App\Api\Controller;

use Apitte\Core\Annotation\Controller as Api;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use SixtyEightPublishers\HealthCheck\HealthCheckerInterface;

/**
 * @Api\Path("/health-check")
 */
final class HealthCheckController extends AbstractController
{
    public function __construct(
        private readonly HealthCheckerInterface $healthChecker,
    ) {}

    /**
     * @Api\Path("/")
     * @Api\Method("GET")
     */
    public function index(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        $result = $this->healthChecker->check();

        return $response
            ->withStatus($result->isOk() ? ApiResponse::S200_OK : ApiResponse::S503_SERVICE_UNAVAILABLE)
            ->writeJsonObject($result);
    }
}
