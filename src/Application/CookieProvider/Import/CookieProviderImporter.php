<?php

declare(strict_types=1);

namespace App\Application\CookieProvider\Import;

use App\ReadModel\Project\ProjectView;
use App\Application\Import\ImporterResult;
use App\Application\DataReader\RowInterface;
use App\Application\Import\ImporterInterface;
use App\ReadModel\Project\FindProjectsByCodesQuery;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\ReadModel\CookieProvider\GetCookieProviderByCodeQuery;
use App\Domain\CookieProvider\Command\CreateCookieProviderCommand;
use App\Domain\CookieProvider\Command\UpdateCookieProviderCommand;
use App\Domain\Project\Command\AddCookieProvidersToProjectCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

final class CookieProviderImporter implements ImporterInterface
{
	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 */
	public function __construct(CommandBusInterface $commandBus, QueryBusInterface $queryBus)
	{
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
	}

	/**
	 * {@inheritDoc}
	 */
	public function accepts(RowInterface $row): bool
	{
		return $row->data() instanceof CookieProviderData;
	}

	/**
	 * {@inheritDoc}
	 */
	public function import(RowInterface $row): ImporterResult
	{
		$data = $row->data();
		assert($data instanceof CookieProviderData);

		$result = ImporterResult::success(sprintf(
			'Provider "%s" imported',
			$data->code
		));

		$cookieProviderView = $this->queryBus->dispatch(GetCookieProviderByCodeQuery::create($data->code));
		[$commands, $result] = $cookieProviderView instanceof CookieProviderView
			? $this->prepareUpdate($data, $cookieProviderView, $result)
			: $this->prepareInsert($data, $result);

		foreach ($commands as $command) {
			$this->commandBus->dispatch($command);
		}

		return $result;
	}

	/**
	 * @param \App\Application\CookieProvider\Import\CookieProviderData $data
	 * @param \App\Application\Import\ImporterResult                    $result
	 *
	 * @return array
	 */
	private function prepareInsert(CookieProviderData $data, ImporterResult $result): array
	{
		$cookieProviderId = CookieProviderId::new();
		$createCommand = CreateCookieProviderCommand::create(
			$data->code,
			$data->type,
			$data->name,
			$data->link,
			$data->purpose,
			FALSE,
			$cookieProviderId->toString()
		);

		[$commands, $result] = $this->prepareUpdateProjects($data, $cookieProviderId, $result);

		return [array_merge([$createCommand], $commands), $result];
	}

	/**
	 * @param \App\Application\CookieProvider\Import\CookieProviderData $data
	 * @param \App\ReadModel\CookieProvider\CookieProviderView          $view
	 * @param \App\Application\Import\ImporterResult                    $result
	 *
	 * @return array
	 */
	private function prepareUpdate(CookieProviderData $data, CookieProviderView $view, ImporterResult $result): array
	{
		$updateCommand = UpdateCookieProviderCommand::create($view->id->toString())
			->withName($data->name)
			->withLink($data->link)
			->withPurposes($data->purpose);

		if ($view->private) {
			if (!$view->type->equals(ProviderType::fromValue($data->type))) {
				$result = $result->withWarning(sprintf(
					'Provider "%s" is a project\'s main provider and the type can not be changed (current: %s, imported: %s)',
					$data->code,
					$view->type->value(),
					$data->type
				));
			}

			$result = $result->withWarning(sprintf(
				'Skipping field "projects" for the provider "%s", associations can not be managed because the provider is a project\'s main provider',
				$data->code
			));

			return [[$updateCommand], $result];
		}

		$updateCommand = $updateCommand->withType($data->type);
		[$commands, $result] = $this->prepareUpdateProjects($data, $view->id, $result);

		return [array_merge([$updateCommand], $commands), $result];
	}

	/**
	 * @param \App\Application\CookieProvider\Import\CookieProviderData $data
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId   $cookieProviderId
	 * @param \App\Application\Import\ImporterResult                    $result
	 *
	 * @return array
	 */
	private function prepareUpdateProjects(CookieProviderData $data, CookieProviderId $cookieProviderId, ImporterResult $result): array
	{
		$found = array_fill_keys($data->projects, TRUE);
		$commands = [];

		foreach ($this->queryBus->dispatch(FindProjectsByCodesQuery::create($data->projects)) as $projectView) {
			assert($projectView instanceof ProjectView);

			$found[$projectView->code->value()] = NULL;
			$commands[] = AddCookieProvidersToProjectCommand::create($projectView->id->toString(), $cookieProviderId->toString());
		}

		$notFound = array_keys(array_filter($found));

		if (0 < count($notFound)) {
			$result = $result->withWarning(sprintf(
				'Associated projects [%s] not found',
				implode(', ', $notFound)
			));
		}

		return [$commands, $result];
	}
}
