<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm\Callback;

use App\Application\DataProcessor\DataProcessFactory;
use App\ReadModel\CookieProvider\CookieProviderExportQuery;
use App\Application\CookieProvider\Import\CookieProviderData;
use App\Application\DataProcessor\Read\Resource\QueryResource;

final class CookieProvidersExportCallback implements ExportCallbackInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function name(): string
	{
		return 'cookie_providers';
	}

	/**
	 * {@inheritDoc}
	 */
	public function __invoke(DataProcessFactory $dataProcessFactory, string $format, array $options): string
	{
		return $dataProcessFactory->fromResource('query', QueryResource::create(CookieProviderExportQuery::create()))
			->withDescriptor(CookieProviderData::describe())
			->writeToString($format, $options);
	}
}
