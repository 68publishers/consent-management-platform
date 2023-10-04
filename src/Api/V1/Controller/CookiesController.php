<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use Apitte\Core\Annotation\Controller as Api;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Api\Cache\Etag;
use App\Api\Cache\EtagStoreInterface;
use App\Api\V1\RequestBody\CookiesRequestBody;
use App\Application\Cookie\Template;
use App\Application\Cookie\TemplateArguments;
use App\Application\Cookie\TemplateRendererInterface;
use App\Application\GlobalSettings\EnabledEnvironmentsResolver;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\Project\ValueObject\Environments;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Locale;
use App\ReadModel\Cookie\CookieApiView;
use App\ReadModel\Cookie\FindCookiesForApiQuery;
use App\ReadModel\Project\GetProjectByCodeQuery;
use App\ReadModel\Project\GetProjectTemplateByCodeAndLocaleWithFallbackQuery;
use App\ReadModel\Project\ProjectTemplateView;
use App\ReadModel\Project\ProjectView;
use JsonException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch;

/**
 * @Api\Path("/cookies")
 */
final class CookiesController extends AbstractV1Controller
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly TemplateRendererInterface $templateRenderer,
        private readonly GlobalSettingsInterface $globalSettings,
        private readonly EtagStoreInterface $etagStore,
    ) {}

    public static function getTemplateUrl(string $projectCode, ?string $locale = null): string
    {
        return sprintf(
            '/api/v1/cookies/%s/template%s',
            $projectCode,
            null !== $locale ? '?locale=' . $locale : '',
        );
    }

    /**
     * @Api\Path("/{project}")
     * @Api\Method("OPTIONS")
     * @Api\RequestParameters({
     *      @Api\RequestParameter(name="project", type="string", in="path", description="Project code"),
     * })
     * @Api\RequestBody(entity="App\Api\V1\RequestBody\CookiesRequestBody", required=true)
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
     * @throws JsonException
     */
    public function getJson(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');

        $projectCode = $request->getParameter('project');
        $requestEntity = $request->getEntity();
        assert($requestEntity instanceof CookiesRequestBody);

        $etagKey = sprintf(
            '%s/%s/%s/[%s]/json',
            $projectCode,
            $requestEntity->locale ?? '_',
            $requestEntity->environment ?? '__default__',
            implode(',', (array) $requestEntity->category),
        );

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

        $errorResponse = $this->tryCreateErrorResponseOnInvalidEnvironment($project->environments, $requestEntity, $response);

        if (null !== $errorResponse) {
            return $errorResponse;
        }

        $locale = null !== $requestEntity->locale && $project->locales->locales()->has(Locale::fromValue($requestEntity->locale))
            ? Locale::fromValue($requestEntity->locale)
            : $project->locales->defaultLocale();

        $responseBody = json_encode([
            'status' => 'success',
            'data' => $this->getCookiesData($project->id, $locale, $project->locales->defaultLocale(), $requestEntity->category, $requestEntity->environment),
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
     * @throws JsonException
     */
    public function getTemplate(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');

        $projectCode = $request->getParameter('project');
        $requestEntity = $request->getEntity();
        assert($requestEntity instanceof CookiesRequestBody);

        $etagKey = sprintf(
            '%s/%s/%s/[%s]/html',
            $projectCode,
            $requestEntity->locale ?? '_',
            $requestEntity->environment ?? '__default__',
            implode(',', (array) $requestEntity->category),
        );

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

        $errorResponse = $this->tryCreateErrorResponseOnInvalidEnvironment($projectTemplate->environments, $requestEntity, $response);

        if (null !== $errorResponse) {
            return $errorResponse;
        }

        $locale = null !== $requestEntity->locale && $projectTemplate->projectLocalesConfig->locales()->has(Locale::fromValue($requestEntity->locale))
            ? Locale::fromValue($requestEntity->locale)
            : $projectTemplate->projectLocalesConfig->defaultLocale();

        $data = $this->getCookiesData($projectTemplate->projectId, $locale, $projectTemplate->projectLocalesConfig->defaultLocale(), $requestEntity->category, $requestEntity->environment);
        $data = json_encode($data, JSON_THROW_ON_ERROR);
        $data = json_decode($data, false, 512, JSON_THROW_ON_ERROR);

        $template = Template::create(
            $projectTemplate->projectId->toString(),
            $projectTemplate->template->value(),
            TemplateArguments::create($data->providers, $data->cookies, $requestEntity->environment),
        );

        $responseBody = $this->templateRenderer->render($template);

        $response = $response->withStatus(ApiResponse::S200_OK)
            ->withHeader('Content-Type', 'text/html')
            ->writeBody($responseBody);

        return $this->applyCacheHeaders($etagKey, $responseBody, $response);
    }

    private function isNotModified(string $etagKey, ApiRequest $request): bool
    {
        if (!$this->globalSettings->apiCache()->useEntityTag()) {
            return false;
        }

        $etag = $this->etagStore->get($etagKey);

        return $etag && $etag->isNotModified($request);
    }

    private function applyCacheHeaders(string $etagKey, string $responseBody, ApiResponse $response): ApiResponse
    {
        $apiCache = $this->globalSettings->apiCache();
        $cacheControlHeader = $apiCache->cacheControlHeader();

        if (null !== $cacheControlHeader) {
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
     * @param mixed $categories
     */
    private function getCookiesData(ProjectId $projectId, Locale $locale, ?Locale $defaultLocale, string|array|null $categories = null, ?string $environment = null): array
    {
        $data = [
            'providers' => [],
            'cookies' => [],
        ];

        $query = FindCookiesForApiQuery::create($projectId->toString(), null !== $defaultLocale && $defaultLocale->equals($locale) ? null : $locale->value())
            ->withBatchSize(100);

        if (null !== $categories) {
            $query = $query->withCategoryCodes((array) $categories);
        }

        if (null !== $environment) {
            $query = $query->withEnvironment($environment);
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

    private function tryCreateErrorResponseOnInvalidEnvironment(Environments $projectEnvironments, CookiesRequestBody $body, ApiResponse $response): ?ApiResponse
    {
        if (null === $body->environment || '' === $body->environment) {
            return null;
        }

        $environments = EnabledEnvironmentsResolver::resolveProjectEnvironments(
            globalSettingsEnvironments: $this->globalSettings->environments(),
            projectEnvironments: $projectEnvironments,
        );

        foreach ($environments as $environment) {
            if ($environment->code === $body->environment) {
                return null;
            }
        }

        return $response->withStatus(ApiResponse::S400_BAD_REQUEST)
            ->writeJsonBody([
                'status' => 'error',
                'data' => [
                    'code' => ApiResponse::S400_BAD_REQUEST,
                    'error' => sprintf(
                        'Project does not have the "%s" environment.',
                        $body->environment,
                    ),
                ],
            ]);
    }
}
