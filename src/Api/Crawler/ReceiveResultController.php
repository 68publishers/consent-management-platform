<?php

declare(strict_types=1);

namespace App\Api\Crawler;

use Exception;
use Throwable;
use DateTimeImmutable;
use Nette\Utils\Strings;
use Psr\Log\LoggerInterface;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Annotation\Controller as Api;
use App\Application\Crawler\CrawlerClientProvider;
use Apitte\Core\Exception\Api\ClientErrorException;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Application\CookieSuggestion\CookieSuggestionsStoreInterface;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ResponseBody\ScenarioResponseBody;

/**
 * @Api\Path("/receive-result")
 */
final class ReceiveResultController extends AbstractCrawlerController
{
	private GlobalSettingsInterface $globalSettings;

	private CrawlerClientProvider $crawlerClientProvider;

	private CookieSuggestionsStoreInterface $cookieSuggestionsStore;

	private LoggerInterface $logger;

	public function __construct(
		GlobalSettingsInterface $globalSettings,
		CrawlerClientProvider $crawlerClientProvider,
		CookieSuggestionsStoreInterface $cookieSuggestionsStore,
		LoggerInterface $logger
	) {
		$this->globalSettings = $globalSettings;
		$this->crawlerClientProvider = $crawlerClientProvider;
		$this->cookieSuggestionsStore = $cookieSuggestionsStore;
		$this->logger = $logger;
	}

	/**
	 * @Api\Path("/")
	 * @Api\Method("POST")
	 *
	 * @throws Exception
	 */
	public function receiveResult(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$authenticationResponse = $this->doAuthentication($request, $response);

		if (NULL !== $authenticationResponse) {
			return $authenticationResponse;
		}


		try {
			$scenarioResponseBody = $this->crawlerClientProvider->get()
				->getSerializer()
				->deserialize($request->getBody()->getContents(), ScenarioResponseBody::class);

			assert($scenarioResponseBody instanceof ScenarioResponseBody);
		} catch (Throwable $e) {
			$error = new ClientErrorException(sprintf(
				'Unable to deserialize received crawler result: %s',
				$e->getMessage()
			), ApiResponse::S400_BAD_REQUEST, $e);

			$this->logger->error((string) $error);

			throw $error;
		}

		if (!isset($scenarioResponseBody->flags['projectId'])) {
			$error = new ClientErrorException('Unable to process received crawler result: Missing flag "projectId".', ApiResponse::S400_BAD_REQUEST);

			$this->logger->error((string) $error);

			throw $error;
		}

		$acceptedCategories = [];

		foreach ($scenarioResponseBody->flags as $flagName => $flagValue) {
			if (Strings::startsWith($flagName, 'category.') && '1' === $flagValue) {
				$acceptedCategories[] = substr($flagName, 9);
			}
		}

		$this->cookieSuggestionsStore->storeCrawledCookies(
			$scenarioResponseBody->name,
			$scenarioResponseBody->flags['projectId'],
			$acceptedCategories,
			$scenarioResponseBody->finishedAt ?? new DateTimeImmutable('now'),
			$scenarioResponseBody->results->cookies,
		);

		return $response->withStatus(ApiResponse::S200_OK)
			->writeJsonBody([
				'status' => 'success',
				'data' => [],
			]);
	}

	private function doAuthentication(ApiRequest $request, ApiResponse $response): ?ApiResponse
	{
		$authorizationHeader = $request->getHeader('Authorization')[0] ?? '';

		if (empty($authorizationHeader)) {
			return $response->withStatus(ApiResponse::S401_UNAUTHORIZED)
				->writeJsonBody([
					'status' => 'error',
					'data' => [
						'code' => ApiResponse::S401_UNAUTHORIZED,
						'error' => 'Missing Authorization header',
					],
				]);
		}

		$methodAndToken = explode(' ', $authorizationHeader, 2);
		$credentials = 2 === count($methodAndToken) ? explode(':', (string) base64_decode($methodAndToken[1]), 2) : [];

		if ('Basic' !== ($methodAndToken[0] ?? '') || 2 !== count($credentials)) {
			return $response->withStatus(ApiResponse::S401_UNAUTHORIZED)
				->writeJsonBody([
					'status' => 'error',
					'data' => [
						'code' => ApiResponse::S401_UNAUTHORIZED,
						'error' => 'Malformed Authorization header',
					],
				]);
		}

		$crawlerSettings = $this->globalSettings->crawlerSettings();
		[$username, $callbackUriToken] = $credentials;

		if ($username !== $crawlerSettings->username() || $callbackUriToken !== $crawlerSettings->callbackUriToken()) {
			return $response->withStatus(ApiResponse::S401_UNAUTHORIZED)
				->writeJsonBody([
					'status' => 'error',
					'data' => [
						'code' => ApiResponse::S401_UNAUTHORIZED,
						'error' => 'Unauthorized',
					],
				]);
		}

		return NULL;
	}
}
