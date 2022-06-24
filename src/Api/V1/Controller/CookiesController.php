<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Application\Cookie\Template;
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

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 * @param \App\Application\Cookie\TemplateRendererInterface              $templateRenderer
	 */
	public function __construct(QueryBusInterface $queryBus, TemplateRendererInterface $templateRenderer)
	{
		$this->queryBus = $queryBus;
		$this->templateRenderer = $templateRenderer;
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
	 */
	public function getJson(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$response = $response->withHeader('Access-Control-Allow-Origin', '*');
		$project = $this->queryBus->dispatch(GetProjectByCodeQuery::create($request->getParameter('project')));

		$requestEntity = $request->getEntity();
		assert($requestEntity instanceof CookiesRequestBody);

		if (!$project instanceof ProjectView) {
			return $response->withStatus(ApiResponse::S422_UNPROCESSABLE_ENTITY)
				->writeJsonBody([
					'status' => 'error',
					'data' => [
						'code' => ApiResponse::S422_UNPROCESSABLE_ENTITY,
						'error' => 'Project does not exist.',
					],
				]);
		}

		$locale = NULL !== $requestEntity->locale && $project->locales->locales()->has(Locale::fromValue($requestEntity->locale))
			? Locale::fromValue($requestEntity->locale)
			: $project->locales->defaultLocale();

		return $response->withStatus(ApiResponse::S200_OK)
			->writeJsonBody([
				'status' => 'success',
				'data' => $this->getCookiesData($project->id, $locale, $project->locales->defaultLocale(), $requestEntity->category),
			]);
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
		$requestEntity = $request->getEntity();
		assert($requestEntity instanceof CookiesRequestBody);

		$projectTemplate = $this->queryBus->dispatch(GetProjectTemplateByCodeAndLocaleWithFallbackQuery::create($request->getParameter('project'), $requestEntity->locale));

		if (!$projectTemplate instanceof ProjectTemplateView) {
			return $response->withStatus(ApiResponse::S422_UNPROCESSABLE_ENTITY)
				->writeJsonBody([
					'status' => 'error',
					'data' => [
						'code' => ApiResponse::S422_UNPROCESSABLE_ENTITY,
						'error' => 'Project does not exist.',
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

		return $response->withStatus(ApiResponse::S200_OK)
			->withHeader('Content-Type', 'text/html')
			->writeBody($this->templateRenderer->render($template));
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
