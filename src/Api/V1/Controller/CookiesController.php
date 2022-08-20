<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use App\Api\Cache\Etag;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Application\Cookie\Template;
use App\Api\Cache\EtagStoreInterface;
use App\ReadModel\Project\ProjectView;
use App\ReadModel\Cookie\CookieApiView;
use App\Domain\Shared\ValueObject\Locale;
use Apitte\Core\Annotation\Controller as Api;
use App\Application\Cookie\TemplateArguments;
use App\Domain\Project\ValueObject\ProjectId;
use App\Api\V1\RequestBody\CookiesRequestBody;
use App\ReadModel\Project\ProjectTemplateView;
use App\ReadModel\Cookie\FindCookiesForApiQuery;
use App\ReadModel\Project\GetProjectByCodeQuery;
use App\Application\Cookie\TemplateRendererInterface;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch;
use App\ReadModel\Project\GetProjectTemplateByCodeAndLocaleWithFallbackQuery;

/**
 * @Api\Path("/cookies")
 */
final class CookiesController extends AbstractV1Controller
{
	private QueryBusInterface $queryBus;

	private TemplateRendererInterface $templateRenderer;

	private GlobalSettingsInterface $globalSettings;

	private EtagStoreInterface $etagStore;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 * @param \App\Application\Cookie\TemplateRendererInterface              $templateRenderer
	 * @param \App\Application\GlobalSettings\GlobalSettingsInterface        $globalSettings
	 * @param \App\Api\Cache\EtagStoreInterface                              $etagStore
	 */
	public function __construct(QueryBusInterface $queryBus, TemplateRendererInterface $templateRenderer, GlobalSettingsInterface $globalSettings, EtagStoreInterface $etagStore)
	{
		$this->queryBus = $queryBus;
		$this->templateRenderer = $templateRenderer;
		$this->globalSettings = $globalSettings;
		$this->etagStore = $etagStore;
	}

	/**
	 * @param string      $projectCode
	 * @param string|NULL $locale
	 *
	 * @return string
	 */
	public static function getTemplateUrl(string $projectCode, ?string $locale = NULL): string
	{
		return sprintf(
			'/api/v1/cookies/%s/template%s',
			$projectCode,
			NULL !== $locale ? '?locale=' . $locale : ''
		);
	}

	/**
	 * @Api\Path("/{project}")
	 * @Api\Method("OPTIONS")
	 * @Api\RequestParameters({
	 *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
	 * })
	 * @Api\RequestBody(entity="App\Api\V1\RequestBody\CookiesRequestBody", required=true)
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 */
	public function optionsJson(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
			->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
			->withHeader('Access-Control-Max-Age', '86400')
			->withHeader('Content-Type', 'application/json')
			->withStatus($response::S204_NO_CONTENT);
	}

	/**
	 * @Api\Path("/{project}/template")
	 * @Api\Method("OPTIONS")
	 * @Api\RequestParameters({
	 *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
	 * })
	 * @Api\RequestBody(entity="App\Api\V1\RequestBody\CookiesRequestBody", required=true)
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 */
	public function optionsTemplate(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
			->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
			->withHeader('Access-Control-Max-Age', '86400')
			->withHeader('Content-Type', 'text/html')
			->withStatus($response::S204_NO_CONTENT);
	}

	/**
	 * @Api\Path("/{project}")
	 * @Api\Method("GET")
	 * @Api\RequestParameters({
	 *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
	 * })
	 * @Api\RequestBody(entity="App\Api\V1\RequestBody\CookiesRequestBody", required=true)
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 * @throws \JsonException
	 */
	public function getJson(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$response = $response->withHeader('Access-Control-Allow-Origin', '*');

		$projectCode = $request->getParameter('project');
		$requestEntity = $request->getEntity();
		assert($requestEntity instanceof CookiesRequestBody);

		$etagKey = sprintf('%s/%s/[%s]/json', $projectCode, $requestEntity->locale ?? '_', implode(',', (array) $requestEntity->category));

		if ($this->isNotModified($etagKey, $request)) {
			return $response->withStatus(ApiResponse::S304_NOT_MODIFIED);
		}

		$project = $this->queryBus->dispatch(GetProjectByCodeQuery::create($projectCode));

		if (!$project instanceof ProjectView) {
			return $response->withStatus(ApiResponse::S404_NOT_FOUND)
				->writeJsonBody([
					'status' => 'error',
					'data' => [
						'code' => ApiResponse::S404_NOT_FOUND,
						'error' => 'Project not found.',
					],
				]);
		}

		$locale = NULL !== $requestEntity->locale && $project->locales->locales()->has(Locale::fromValue($requestEntity->locale))
			? Locale::fromValue($requestEntity->locale)
			: $project->locales->defaultLocale();

		$responseBody = json_encode([
			'status' => 'success',
			'data' => $this->getCookiesData($project->id, $locale, $project->locales->defaultLocale(), $requestEntity->category),
		], JSON_THROW_ON_ERROR);

		$response = $response->withStatus(ApiResponse::S200_OK)
			->withHeader('Content-Type', 'application/json')
			->writeBody($responseBody);

		return $this->applyCacheHeaders($etagKey, $responseBody, $response);
	}

	/**
	 * @Api\Path("/{project}/template")
	 * @Api\Method("GET")
	 * @Api\RequestParameters({
	 *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
	 * })
	 * @Api\RequestBody(entity="App\Api\V1\RequestBody\CookiesRequestBody", required=true)
	 *
	 * @param \Apitte\Core\Http\ApiRequest  $request
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 * @throws \JsonException
	 */
	public function getTemplate(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$response = $response->withHeader('Access-Control-Allow-Origin', '*');

		$projectCode = $request->getParameter('project');
		$requestEntity = $request->getEntity();
		assert($requestEntity instanceof CookiesRequestBody);

		$etagKey = sprintf('%s/%s/[%s]/html', $projectCode, $requestEntity->locale ?? '_', implode(',', (array) $requestEntity->category));

		if ($this->isNotModified($etagKey, $request)) {
			return $response->withStatus(ApiResponse::S304_NOT_MODIFIED);
		}

		$projectTemplate = $this->queryBus->dispatch(GetProjectTemplateByCodeAndLocaleWithFallbackQuery::create($projectCode, $requestEntity->locale));

		if (!$projectTemplate instanceof ProjectTemplateView) {
			return $response->withStatus(ApiResponse::S404_NOT_FOUND)
				->writeJsonBody([
					'status' => 'error',
					'data' => [
						'code' => ApiResponse::S404_NOT_FOUND,
						'error' => 'Project not found.',
					],
				]);
		}

		$locale = NULL !== $requestEntity->locale && $projectTemplate->projectLocalesConfig->locales()->has(Locale::fromValue($requestEntity->locale))
			? Locale::fromValue($requestEntity->locale)
			: $projectTemplate->projectLocalesConfig->defaultLocale();

		$data = $this->getCookiesData($projectTemplate->projectId, $locale, $projectTemplate->projectLocalesConfig->defaultLocale(), $requestEntity->category);
		$data = json_encode($data, JSON_THROW_ON_ERROR);
		$data = json_decode($data, FALSE, 512, JSON_THROW_ON_ERROR);

		$template = Template::create(
			$projectTemplate->projectId->toString(),
			$projectTemplate->template->value(),
			TemplateArguments::create($data->providers, $data->cookies)
		);

		$responseBody = $this->templateRenderer->render($template);

		$response = $response->withStatus(ApiResponse::S200_OK)
			->withHeader('Content-Type', 'text/html')
			->writeBody($responseBody);

		return $this->applyCacheHeaders($etagKey, $responseBody, $response);
	}

	/**
	 * @param string                       $etagKey
	 * @param \Apitte\Core\Http\ApiRequest $request
	 *
	 * @return bool
	 */
	private function isNotModified(string $etagKey, ApiRequest $request): bool
	{
		if (!$this->globalSettings->apiCache()->useEntityTag()) {
			return FALSE;
		}

		$etag = $this->etagStore->get($etagKey);

		return $etag && $etag->isNotModified($request);
	}

	/**
	 * @param string                        $etagKey
	 * @param string                        $responseBody
	 * @param \Apitte\Core\Http\ApiResponse $response
	 *
	 * @return \Apitte\Core\Http\ApiResponse
	 */
	private function applyCacheHeaders(string $etagKey, string $responseBody, ApiResponse $response): ApiResponse
	{
		$apiCache = $this->globalSettings->apiCache();
		$cacheControlHeader = $apiCache->cacheControlHeader();

		if (NULL !== $cacheControlHeader) {
			$response = $response->withHeader('Cache-Control', $cacheControlHeader);
		}

		if ($apiCache->useEntityTag()) {
			$etag = Etag::fromValidator(hash('sha256', $responseBody));
			$response = $etag->addToResponse($response);

			$this->etagStore->save($etagKey, $etag);
		}

		return $response;
	}

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId  $projectId
	 * @param \App\Domain\Shared\ValueObject\Locale      $locale
	 * @param \App\Domain\Shared\ValueObject\Locale|NULL $defaultLocale
	 * @param mixed                                      $categories
	 *
	 * @return array
	 */
	private function getCookiesData(ProjectId $projectId, Locale $locale, ?Locale $defaultLocale, $categories = NULL): array
	{
		$data = [
			'providers' => [],
			'cookies' => [],
		];

		$query = FindCookiesForApiQuery::create($projectId->toString(), NULL !== $defaultLocale && $defaultLocale->equals($locale) ? NULL : $locale->value())
			->withBatchSize(100);

		if (NULL !== $categories) {
			$query = $query->withCategoryCodes((array) $categories);
		}

		foreach ($this->queryBus->dispatch($query) as $batch) {
			assert($batch instanceof Batch);

			foreach ($batch->results() as $result) {
				assert($result instanceof CookieApiView);

				$data['cookies'][] = $result->serializeCookie($locale->value());

				if (!isset($data['providers'][$result->cookieProviderCode->value()])) {
					$data['providers'][$result->cookieProviderCode->value()] = $result->serializeCookieProvider();
				}
			}
		}

		# sort providers
		$providers = array_values($data['providers']);
		$providerNames = array_column($providers, 'name');

		array_multisort($providerNames, SORT_ASC, $providers);

		$data['providers'] = $providers;

		# sort cookies
		$cookies = array_values($data['cookies']);
		$cookieNames = array_map('strtolower', array_column($cookies, 'name'));

		array_multisort($cookieNames, SORT_ASC, $cookies);

		$data['cookies'] = $cookies;

		return $data;
	}
}
