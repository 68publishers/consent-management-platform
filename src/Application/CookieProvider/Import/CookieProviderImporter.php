<?php

declare(strict_types=1);

namespace App\Application\CookieProvider\Import;

use App\Application\Import\RowResult;
use App\Application\Import\ImporterResult;
use App\Application\Import\AbstractImporter;
use App\Application\DataProcessor\RowInterface;
use App\ReadModel\Project\ProjectPermissionView;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\ReadModel\CookieProvider\FindCookieProvidersByCodesQuery;
use App\Domain\CookieProvider\Command\CreateCookieProviderCommand;
use App\Domain\CookieProvider\Command\UpdateCookieProviderCommand;
use App\Domain\Project\Command\AddCookieProvidersToProjectCommand;
use App\Domain\Project\Command\RemoveCookieProvidersFromProjectCommand;
use App\ReadModel\Project\FindAllProjectsWithPossibleAssociationWithCookieProviderQuery;

final class CookieProviderImporter extends AbstractImporter
{
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
	public function import(array $rows): ImporterResult
	{
		$result = ImporterResult::of();
		$existingProviders = $this->findExistingProviders($rows);

		foreach ($rows as $row) {
			$result = $result->with($this->wrapRowImport($row, function (RowInterface $row) use ($existingProviders): RowResult {
				$data = $row->data();
				assert($data instanceof CookieProviderData);

				$result = RowResult::success($row->index(), sprintf(
					'Provider "%s" imported',
					$data->code
				));
				$code = mb_strtolower($data->code);

				[$commands, $result] = isset($existingProviders[$code])
					? $this->prepareUpdate($data, $existingProviders[$code], $result)
					: $this->prepareInsert($data, $result);

				foreach ($commands as $command) {
					$this->commandBus->dispatch($command);
				}

				return $result;
			}));
		}

		return $result;
	}

	/**
	 * @param \App\Application\CookieProvider\Import\CookieProviderData $data
	 * @param \App\Application\Import\RowResult                         $result
	 *
	 * @return array
	 */
	private function prepareInsert(CookieProviderData $data, RowResult $result): array
	{
		$cookieProviderId = CookieProviderId::new();
		$createCommand = CreateCookieProviderCommand::create(
			$data->code,
			$data->type,
			$data->name,
			$data->link,
			$data->purpose,
			FALSE,
			$data->active,
			$cookieProviderId->toString()
		);

		[$commands, $result] = $this->prepareUpdateProjects($data, $cookieProviderId, $result);

		return [array_merge([$createCommand], $commands), $result];
	}

	/**
	 * @param \App\Application\CookieProvider\Import\CookieProviderData $data
	 * @param \App\ReadModel\CookieProvider\CookieProviderView          $view
	 * @param \App\Application\Import\RowResult                         $result
	 *
	 * @return array
	 */
	private function prepareUpdate(CookieProviderData $data, CookieProviderView $view, RowResult $result): array
	{
		$updateCommand = UpdateCookieProviderCommand::create($view->id->toString())
			->withName($data->name)
			->withLink($data->link)
			->withActive($data->active)
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

			if (!empty($data->projects)) {
				$result = $result->withWarning(sprintf(
					'Skipping field "projects" for the provider "%s", associations can not be managed because the provider is a project\'s main provider',
					$data->code
				));
			}

			return [[$updateCommand], $result];
		}

		$updateCommand = $updateCommand->withType($data->type);
		[$commands, $result] = $this->prepareUpdateProjects($data, $view->id, $result);

		return [array_merge([$updateCommand], $commands), $result];
	}

	/**
	 * @param \App\Application\CookieProvider\Import\CookieProviderData $data
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId   $cookieProviderId
	 * @param \App\Application\Import\RowResult                         $result
	 *
	 * @return array
	 */
	private function prepareUpdateProjects(CookieProviderData $data, CookieProviderId $cookieProviderId, RowResult $result): array
	{
		$loweCaseCodes = array_map('strtolower', $data->projects);
		$codes = array_combine($loweCaseCodes, $data->projects);
		$commands = [];

		foreach ($this->queryBus->dispatch(FindAllProjectsWithPossibleAssociationWithCookieProviderQuery::create($cookieProviderId->toString(), NULL)) as $projectPermissionView) {
			assert($projectPermissionView instanceof ProjectPermissionView);

			$loweCaseCode = mb_strtolower($projectPermissionView->projectCode->value());

			if ($projectPermissionView->permission && !in_array($loweCaseCode, $loweCaseCodes, TRUE)) {
				$commands[] = RemoveCookieProvidersFromProjectCommand::create($projectPermissionView->projectId->toString(), $cookieProviderId->toString());
			}

			if (!$projectPermissionView->permission && in_array($loweCaseCode, $loweCaseCodes, TRUE)) {
				$commands[] = AddCookieProvidersToProjectCommand::create($projectPermissionView->projectId->toString(), $cookieProviderId->toString());
			}

			$codes[$loweCaseCode] = NULL;
		}

		$notFound = array_filter($codes);

		if (0 < count($notFound)) {
			$result = $result->withWarning(sprintf(
				'Associated projects [%s] not found',
				implode(', ', $notFound)
			));
		}

		return [$commands, $result];
	}

	/**
	 * @param array $rows
	 *
	 * @return \App\ReadModel\CookieProvider\CookieProviderView[]
	 */
	private function findExistingProviders(array $rows): array
	{
		$existingProviders = $this->queryBus->dispatch(FindCookieProvidersByCodesQuery::create(
			array_unique(
				array_map(
					static fn (RowInterface $row): string => $row->data()->get('code'),
					$rows
				)
			)
		));

		$keys = array_map(
			static fn (CookieProviderView $view): string => mb_strtolower($view->code->value()),
			$existingProviders
		);

		return array_combine($keys, $existingProviders);
	}
}
