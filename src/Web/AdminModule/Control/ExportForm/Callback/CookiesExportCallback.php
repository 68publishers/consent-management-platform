<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm\Callback;

use App\Application\Cookie\Import\CookieData;
use App\Application\DataProcessor\DataProcessFactory;
use App\Application\DataProcessor\Read\Resource\QueryResource;
use App\ReadModel\Cookie\CookieExportQuery;

final class CookiesExportCallback implements ExportCallbackInterface
{
    public function name(): string
    {
        return 'cookies';
    }

    public function __invoke(DataProcessFactory $dataProcessFactory, string $format, array $options): string
    {
        return $dataProcessFactory->fromResource('query', QueryResource::create(CookieExportQuery::create()))
            ->withDescriptor(CookieData::describe())
            ->writeToString($format, $options);
    }
}
