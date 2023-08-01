<?php

declare(strict_types=1);

namespace App\Application\Project\Import;

use App\Application\DataProcessor\RowInterface;
use App\Application\Import\AbstractImporter;
use App\Application\Import\ImporterResult;
use App\Application\Import\RowResult;
use App\Domain\Project\Command\CreateProjectCommand;
use App\Domain\Project\Command\UpdateProjectCommand;
use App\ReadModel\Project\GetProjectByCodeQuery;
use App\ReadModel\Project\ProjectView;

final class ProjectImporter extends AbstractImporter
{
    public function accepts(RowInterface $row): bool
    {
        return $row->data() instanceof ProjectData;
    }

    public function import(array $rows): ImporterResult
    {
        $result = ImporterResult::of();

        foreach ($rows as $row) {
            $result = $result->with($this->wrapRowImport($row, function (RowInterface $row): RowResult {
                $data = $row->data();
                assert($data instanceof ProjectData);

                $projectView = $this->queryBus->dispatch(GetProjectByCodeQuery::create($data->code));

                $result = RowResult::success($row->index(), sprintf(
                    'Project "%s" imported',
                    $data->code,
                ));

                $locales = $data->locales;
                $defaultLocale = $data->defaultLocale;

                if (empty($defaultLocale)) {
                    $defaultLocale = (string) reset($locales);
                }

                if ($projectView instanceof ProjectView) {
                    $command = UpdateProjectCommand::create($projectView->id->toString())
                        ->withName($data->name)
                        ->withCode($data->code)
                        ->withDomain($data->domain)
                        ->withColor($data->color)
                        ->withDescription($data->description)
                        ->withActive($data->active)
                        ->withLocales($locales, $defaultLocale);
                } else {
                    $command = CreateProjectCommand::create(
                        $data->name,
                        $data->code,
                        $data->domain,
                        $data->description,
                        $data->color,
                        $data->active,
                        $locales,
                        $defaultLocale,
                    );
                }

                $this->commandBus->dispatch($command);

                return $result;
            }));
        }

        return $result;
    }
}
