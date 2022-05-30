<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\OpenApi\ISchemaBuilder;
use Apitte\Core\Annotation\Controller as API;

/**
 * @Api\Path("/openapi")
 */
final class OpenApiController extends AbstractV1Controller
{
	private ISchemaBuilder $schemaBuilder;

	/**
	 * @param \Apitte\OpenApi\ISchemaBuilder $schemaBuilder
	 */
	public function __construct(ISchemaBuilder $schemaBuilder)
	{
		$this->schemaBuilder = $schemaBuilder;
	}

	/**
	 * @API\Path("/")
	 * @API\Method("GET")
	 */
	public function index(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$openApi = $this->schemaBuilder->build();

		return $response->writeJsonBody($openApi->toArray());
	}
}
