<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\OpenApi\ISchemaBuilder;
use App\Application\OpenApiConfiguration;
use Apitte\Core\Annotation\Controller as API;
use Apitte\Core\Exception\Api\ClientErrorException;

/**
 * @Api\Path("/openapi")
 */
final class OpenApiController extends AbstractV1Controller
{
	public const ENDPOINT = '/api/v1/openapi';

	private ISchemaBuilder $schemaBuilder;

	private OpenApiConfiguration $openApiConfiguration;

	/**
	 * @param \Apitte\OpenApi\ISchemaBuilder        $schemaBuilder
	 * @param \App\Application\OpenApiConfiguration $openApiConfiguration
	 */
	public function __construct(ISchemaBuilder $schemaBuilder, OpenApiConfiguration $openApiConfiguration)
	{
		$this->schemaBuilder = $schemaBuilder;
		$this->openApiConfiguration = $openApiConfiguration;
	}

	/**
	 * @API\Path("/")
	 * @API\Method("GET")
	 */
	public function index(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		if (!$this->openApiConfiguration->enabled()) {
			throw new ClientErrorException('OpenApi is disabled.', ApiResponse::S400_BAD_REQUEST);
		}

		$openApi = $this->schemaBuilder->build();

		return $response->writeJsonBody($openApi->toArray());
	}
}
