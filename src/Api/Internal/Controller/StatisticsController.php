<?php

declare(strict_types=1);

namespace App\Api\Internal\Controller;

use Exception;
use DateTimeZone;
use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeInterface;
use Carbon\CarbonInterface;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Application\Statistics\Period;
use Apitte\Core\Annotation\Controller as Api;
use App\ReadModel\Project\ProjectPermissionView;
use App\ReadModel\Project\FindProjectsAccessibilityByCodeQuery;
use App\Api\Internal\RequestBody\GetProjectStatisticsRequestBody;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Application\Statistics\ProjectStatisticsCalculatorInterface;

/**
 * @Api\Path("/statistics")
 */
final class StatisticsController extends AbstractInternalController
{
	public const ENDPOINT_PROJECTS = '/api/internal/statistics/projects';

	private QueryBusInterface $queryBus;

	private ProjectStatisticsCalculatorInterface $projectStatisticsCalculator;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 * @param \App\Application\Statistics\ProjectStatisticsCalculatorInterface $projectStatisticsCalculator
	 */
	public function __construct(QueryBusInterface $queryBus, ProjectStatisticsCalculatorInterface $projectStatisticsCalculator)
	{
		$this->queryBus = $queryBus;
		$this->projectStatisticsCalculator = $projectStatisticsCalculator;
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
		$requestEntity = $request->getEntity();
		assert($requestEntity instanceof GetProjectStatisticsRequestBody);

		$timezone = new DateTimeZone($requestEntity->timezone);
		$projectCodes = (array) $requestEntity->projects;
		$projectIds = array_fill_keys($projectCodes, NULL);
		$inaccessible = [];

		// check accessibility for requested projects
		foreach ($this->queryBus->dispatch(FindProjectsAccessibilityByCodeQuery::create($requestEntity->userId, $projectCodes)) as $projectPermissionView) {
			assert($projectPermissionView instanceof ProjectPermissionView);

			$projectIds[$projectPermissionView->projectCode->value()] = $projectPermissionView->projectId->toString();

			if (!$projectPermissionView->permission) {
				$inaccessible[] = $projectPermissionView->projectCode->value();
			}
		}

		$projectIds = array_filter($projectIds);

		// are some projects inaccessible for the current user?
		if (0 < count($inaccessible)) {
			return $response->withStatus(ApiResponse::S401_UNAUTHORIZED)
				->writeJsonBody([
					'status' => 'error',
					'data' => [
						'code' => ApiResponse::S401_UNAUTHORIZED,
						'error' => sprintf(
							'Project%s %s are not accessible for the user.',
							1 < count($inaccessible) ? 's' : '',
							implode(', ', $inaccessible)
						),
					],
				]);
		}

		try {
			[$startDate, $endDate] = $this->createRange($requestEntity, $timezone);
		} catch (Exception $e) {
			return $response->withStatus(ApiResponse::S422_UNPROCESSABLE_ENTITY)
				->writeJsonBody([
					'status' => 'error',
					'data' => [
						'code' => ApiResponse::S422_UNPROCESSABLE_ENTITY,
						'error' => 'Invalid date format.',
					],
				]);
		}

		return $response->withStatus(ApiResponse::S200_OK)
			->writeJsonBody([
				'status' => 'success',
				'data' => $this->buildData($projectIds, $requestEntity->locale, $startDate, $endDate, $timezone),
			]);
	}

	/**
	 * @param string[]           $projectIdsByCodes
	 * @param string             $locale
	 * @param \DateTimeImmutable $startDate
	 * @param \DateTimeImmutable $endDate
	 * @param \DateTimeZone      $userTz
	 *
	 * @return array
	 */
	private function buildData(array $projectIdsByCodes, string $locale, DateTimeImmutable $startDate, DateTimeImmutable $endDate, DateTimeZone $userTz): array
	{
		$data = [];

		if (0 >= count($projectIdsByCodes)) {
			return $data;
		}

		$period = Period::create($startDate, $endDate);

		foreach ($projectIdsByCodes as $code => $projectId) {
			$consentStatistics = $this->projectStatisticsCalculator->calculateConsentStatistics($projectId, $period);
			$cookieStatistics = $this->projectStatisticsCalculator->calculateCookieStatistics($projectId, $endDate);
			$lastConsentDate = $this->projectStatisticsCalculator->calculateLastConsentDate($projectId, $endDate);
			$cookieSuggestionStatistics = $this->projectStatisticsCalculator->calculateCookieSuggestionStatistics($projectId);

			if (NULL !== $lastConsentDate) {
				$lastConsentDate = $lastConsentDate->setTimezone($userTz);
			}

			$data[$code] = [
				'allConsents' => [
					'value' => $consentStatistics->totalConsentsStatistics()->currentValue(),
					'percentageDiff' => $consentStatistics->totalConsentsStatistics()->percentageDiff(),
				],
				'uniqueConsents' => [
					'value' => $consentStatistics->uniqueConsentsStatistics()->currentValue(),
					'percentageDiff' => $consentStatistics->uniqueConsentsStatistics()->percentageDiff(),
				],
				'allPositive' => [
					'value' => $consentStatistics->totalConsentsPositivityStatistics()->currentValue(),
					'percentageDiff' => $consentStatistics->totalConsentsPositivityStatistics()->percentageDiff(),
				],
				'uniquePositive' => [
					'value' => $consentStatistics->uniqueConsentsPositivityStatistics()->currentValue(),
					'percentageDiff' => $consentStatistics->uniqueConsentsPositivityStatistics()->percentageDiff(),
				],
				'lastConsent' => [
					'value' => NULL !== $lastConsentDate ? $lastConsentDate->format(DateTimeInterface::ATOM) : NULL,
					'formattedValue' => NULL !== $lastConsentDate ? $lastConsentDate->format('j.n.Y H:i:s') : NULL,
					'text' => NULL !== $lastConsentDate ? Carbon::parse($lastConsentDate)->locale($locale)->ago(CarbonInterface::DIFF_RELATIVE_TO_NOW) : NULL,
				],
				'providers' => [
					'value' => $cookieStatistics->numberOfProviders(),
				],
				'cookies' => [
					'commonValue' => $cookieStatistics->numberOfCommonCookies(),
					'privateValue' => $cookieStatistics->numberOfPrivateCookies(),
				],
				'cookieSuggestions' => [
					'enabled' => NULL !== $cookieSuggestionStatistics,
					'missing' => NULL !== $cookieSuggestionStatistics ? $cookieSuggestionStatistics->missing : 0,
					'unassociated' => NULL !== $cookieSuggestionStatistics ? $cookieSuggestionStatistics->unassociated : 0,
					'problematic' => NULL !== $cookieSuggestionStatistics ? $cookieSuggestionStatistics->problematic : 0,
					'unproblematic' => NULL !== $cookieSuggestionStatistics ? $cookieSuggestionStatistics->unproblematic : 0,
					'ignored' => NULL !== $cookieSuggestionStatistics ? $cookieSuggestionStatistics->ignored : 0,
				],
			];
		}

		return $data;
	}

	/**
	 * @param \App\Api\Internal\RequestBody\GetProjectStatisticsRequestBody $requestBody
	 * @param \DateTimeZone                                                 $userTz
	 *
	 * @return array
	 * @throws \Exception
	 */
	private function createRange(GetProjectStatisticsRequestBody $requestBody, DateTimeZone $userTz): array
	{
		$utc = new DateTimeZone('UTC');

		$startDate = (new DateTimeImmutable($requestBody->startDate, $userTz))
			->setTime(0, 0)
			->setTimezone($utc);

		$endDate = (new DateTimeImmutable($requestBody->endDate, $userTz))
			->setTime(23, 59, 59)
			->setTimezone($utc);

		return $startDate > $endDate ? [$endDate, $startDate] : [$startDate, $endDate];
	}
}
