<?php

declare(strict_types=1);

namespace App\Application\Cookie\Import;

use App\Application\DataProcessor\RowInterface;
use App\Application\Import\AbstractImporter;
use App\Application\Import\ImporterResult;
use App\Application\Import\RowResult;
use App\Domain\Cookie\Command\CreateCookieCommand;
use App\Domain\Cookie\Command\UpdateCookieCommand;
use App\ReadModel\Category\CategoryView;
use App\ReadModel\Category\GetCategoryByCodeQuery;
use App\ReadModel\Cookie\CookieView;
use App\ReadModel\Cookie\GetCookieByNameAndCookieProviderAndCategoryQuery;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\ReadModel\CookieProvider\GetCookieProviderByCodeQuery;

final class CookieImporter extends AbstractImporter
{
    private array $categories = [];

    private array $cookieProviders = [];

    public function accepts(RowInterface $row): bool
    {
        return $row->data() instanceof CookieData;
    }

    public function import(array $rows): ImporterResult
    {
        $result = ImporterResult::of();

        foreach ($rows as $row) {
            $result = $result->with($this->wrapRowImport($row, function (RowInterface $row): RowResult {
                $data = $row->data();
                assert($data instanceof CookieData);

                $cookieProviderView = $this->getCookieProvider($data->provider);

                if (!$cookieProviderView instanceof CookieProviderView) {
                    return RowResult::error($row->index(), sprintf(
                        'Can not import cookie "%s", the provider "%s" not found.',
                        $data->name,
                        $data->provider,
                    ));
                }

                $categoryView = $this->getCategory($data->category);

                if (!$categoryView instanceof CategoryView) {
                    return RowResult::error($row->index(), sprintf(
                        'Can not import cookie "%s", the category "%s" not found.',
                        $data->name,
                        $data->category,
                    ));
                }

                $result = RowResult::success($row->index(), sprintf(
                    'Cookie "%s" for provider "%s" imported',
                    $data->name,
                    $data->provider,
                ));

                $cookieView = $this->queryBus->dispatch(GetCookieByNameAndCookieProviderAndCategoryQuery::create($data->name, $cookieProviderView->id->toString(), $categoryView->id->toString()));
                $environments = empty($data->environments)
                    ? true
                    : $data->environments;

                if ($cookieView instanceof CookieView) {
                    $command = UpdateCookieCommand::create($cookieView->id->toString())
                        ->withName($data->name)
                        ->withActive($data->active)
                        ->withCategoryId($categoryView->id->toString())
                        ->withProcessingTime($data->processingTime)
                        ->withPurposes($data->purpose)
                        ->withEnvironments($environments);

                    if (!empty($data->domain)) {
                        $command = $command->withDomain($data->domain);
                    }
                } else {
                    $command = CreateCookieCommand::create(
                        categoryId: $categoryView->id->toString(),
                        cookieProviderId: $cookieProviderView->id->toString(),
                        name: $data->name,
                        domain: $data->domain,
                        processingTime: $data->processingTime,
                        active: $data->active,
                        purposes: $data->purpose,
                        environments: $environments,
                    );
                }

                $this->commandBus->dispatch($command);

                return $result;
            }));
        }

        return $result;
    }

    private function getCategory(string $code): ?CategoryView
    {
        if (array_key_exists($code, $this->categories)) {
            return $this->categories[$code];
        }

        return $this->categories[$code] = $this->queryBus->dispatch(GetCategoryByCodeQuery::create($code));
    }

    private function getCookieProvider(string $code): ?CookieProviderView
    {
        if (array_key_exists($code, $this->cookieProviders)) {
            return $this->cookieProviders[$code];
        }

        return $this->cookieProviders[$code] = $this->queryBus->dispatch(GetCookieProviderByCodeQuery::create($code));
    }
}
