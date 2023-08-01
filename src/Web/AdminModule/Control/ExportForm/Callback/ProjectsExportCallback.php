<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm\Callback;

use App\Application\DataProcessor\DataProcessFactory;
use App\Application\DataProcessor\Read\Resource\QueryResource;
use App\Application\Project\Import\ProjectData;
use App\ReadModel\Project\ProjectExportQuery;

final class ProjectsExportCallback implements ExportCallbackInterface
{
    public function name(): string
    {
        return 'projects';
    }

    public function __invoke(DataProcessFactory $dataProcessFactory, string $format, array $options): string
    {
        return $dataProcessFactory->fromResource('query', QueryResource::create(ProjectExportQuery::create()))
            ->withDescriptor(ProjectData::describe())
            ->writeToString($format, $options);
    }
}
