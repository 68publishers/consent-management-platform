<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\DataSource;

use Closure;
use DateTime;
use Throwable;
use DateTimeZone;
use DateTimeImmutable;
use DateTimeInterface;
use Ublaboo\DataGrid\Utils\Sorting;
use Ublaboo\DataGrid\DataSource\IDataSource;
use App\Application\Crawler\CrawlerClientProvider;
use App\Application\DataProcessor\Helper\FlatResource;
use App\Application\Crawler\CrawlerNotConfiguredException;
use SixtyEightPublishers\CrawlerClient\CrawlerClientInterface;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerExceptionInterface;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenariosController;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulersController;

final class CrawlerDataSource implements IDataSource
{
	private CrawlerClientProvider $crawlerClientProvider;

	private Closure $getTotalCountCallback;

	private Closure $getDataCallback;

	private array $parameters = [
		'page' => 1,
		'limit' => 1,
		'filter' => [],
	];

	private ?Throwable $error = NULL;

	private function __construct(CrawlerClientProvider $crawlerClientProvider, Closure $getTotalCountCallback, Closure $getDataCallback)
	{
		$this->crawlerClientProvider = $crawlerClientProvider;
		$this->getTotalCountCallback = $getTotalCountCallback;
		$this->getDataCallback = $getDataCallback;
	}

	public static function scenarios(CrawlerClientProvider $crawlerClientProvider): self
	{
		return new self(
			$crawlerClientProvider,
			static function (CrawlerClientInterface $client, array $parameters) {
				return $client
					->getController(ScenariosController::class)
					->listScenarios(1, 1, $parameters['filter'])
					->getBody()
					->totalCount;
			},
			static function (CrawlerClientInterface $client, array $parameters) {
				return $client
					->getController(ScenariosController::class)
					->listScenarios($parameters['page'], $parameters['limit'], $parameters['filter'])
					->getBody()
					->data;
			},
		);
	}

	public static function scenarioSchedulers(CrawlerClientProvider $crawlerClientProvider): self
	{
		return new self(
			$crawlerClientProvider,
			static function (CrawlerClientInterface $client, array $parameters) {
				return $client
					->getController(ScenarioSchedulersController::class)
					->listScenarioSchedulers(1, 1, $parameters['filter'])
					->getBody()
					->totalCount;
			},
			static function (CrawlerClientInterface $client, array $parameters) {
				return $client
					->getController(ScenarioSchedulersController::class)
					->listScenarioSchedulers($parameters['page'], $parameters['limit'], $parameters['filter'])
					->getBody()
					->data;
			},
		);
	}

	/**
	 * @return Throwable|null
	 */
	public function getError(): ?Throwable
	{
		return $this->error;
	}

	public function getCount(): int
	{
		$client = $this->getClient();

		if (NULL === $client) {
			return 0;
		}

		try {
			return ($this->getTotalCountCallback)($client, $this->prepareParameters());
		} catch (ControllerExceptionInterface $e) {
			$this->error = $e;

			return 0;
		}
	}

	public function getData(): array
	{
		$client = $this->getClient();

		if (NULL === $client) {
			return [];
		}

		try {
			return ($this->getDataCallback)($this->getClient(), $this->prepareParameters());
		} catch (ControllerExceptionInterface $e) {
			$this->error = $e;

			return [];
		}
	}

	public function filter(array $filters): void
	{
		foreach ($filters as $filter) {
			if ($filter->isValueSet()) {
				foreach ($filter->getCondition() as $column => $value) {
					$applicableFilters = [
						$column => $value,
					];

					if ($value instanceof DateTimeImmutable || $value instanceof DateTime) {
						$applicableFilters = [
							$column . 'Before' => (clone $value)
								->setTime(23, 59, 59)
								->setTimezone(new DateTimeZone('UTC'))
								->format(DateTimeInterface::ATOM),
							$column . 'After' => (clone $value)
								->setTime(0, 0)
								->setTimezone(new DateTimeZone('UTC'))
								->format(DateTimeInterface::ATOM),
						];
					}

					foreach ($applicableFilters as $col => $val) {
						$this->parameters['filter'][$col] = $val;
					}
				}
			}
		}
	}

	public function filterOne(array $condition): self
	{
		foreach ($condition as $column => $value) {
			$this->parameters['filter'][$column] = $value;
		}

		return $this;
	}

	public function limit(int $offset, int $limit): self
	{
		$this->parameters['page'] = (int) (($offset / $limit) + 1);
		$this->parameters['limit'] = $limit;

		return $this;
	}

	public function sort(Sorting $sorting): self
	{
		# not supported
		return $this;
	}

	private function getClient(): ?CrawlerClientInterface
	{
		try {
			return $this->crawlerClientProvider->get();
		} catch (CrawlerNotConfiguredException $e) {
			$this->error = $e;
		}

		return NULL;
	}

	/**
	 * @return array{
	 *     page: int,
	 *     limit: int,
	 *     filter: array<string, mixed>,
	 * }
	 */
	private function prepareParameters(): array
	{
		return [
			'page' => $this->parameters['page'],
			'limit' => $this->parameters['limit'],
			'filter' => FlatResource::toMultidimensionalArray($this->parameters['filter'], '->'),
		];
	}
}
