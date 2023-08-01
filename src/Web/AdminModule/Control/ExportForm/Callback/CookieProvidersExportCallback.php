<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm\Callback;

use App\Application\CookieProvider\Import\CookieProviderData;
use App\Application\DataProcessor\DataProcessFactory;
use App\Application\DataProcessor\Read\Resource\QueryResource;
use App\ReadModel\CookieProvider\CookieProviderExportQuery;

final class CookieProvidersExportCallback implements ExportCallbackInterface
{
    public function name(): string
    {
        return 'cookie_providers';
    }

    public function __invoke(DataProcessFactory $dataProcessFactory, string $format, array $options): string
    {
        return $dataProcessFactory->fromResource('query', QueryResource::create(CookieProviderExportQuery::create()))
            ->withDescriptor(CookieProviderData::describe())
            ->writeToString($format, $options);
    }
}
