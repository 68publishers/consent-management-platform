<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use App\ReadModel\Cookie\CookieApiView;
use App\Domain\Category\ValueObject\Name as CategoryName;
use App\Domain\Cookie\ValueObject\Purpose as CookiePurpose;
use App\Domain\CookieProvider\ValueObject\Purpose as CookieProviderPurpose;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataTransformerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class CookieApiViewDataTransformer implements ViewDataTransformerInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function canTransform(string $viewClassname, ViewDataInterface $viewData): bool
	{
		return is_a($viewClassname, CookieApiView::class, TRUE) && $viewData instanceof DoctrineViewData;
	}

	/**
	 * {@inheritDoc}
	 */
	public function transform(ViewDataInterface $viewData, ViewFactoryInterface $viewFactory): ViewDataInterface
	{
		if ($viewData->has('cookiePurpose') && !$viewData->get('cookiePurpose') instanceof CookiePurpose) {
			$viewData = $viewData->with('cookiePurpose', CookiePurpose::fromValue($viewData->get('cookiePurpose')));
		}

		if ($viewData->has('cookieProviderPurpose') && !$viewData->get('cookieProviderPurpose') instanceof CookieProviderPurpose) {
			$viewData = $viewData->with('cookieProviderPurpose', CookieProviderPurpose::fromValue($viewData->get('cookieProviderPurpose')));
		}

		if ($viewData->has('categoryName') && !$viewData->get('categoryName') instanceof CategoryName) {
			$viewData = $viewData->with('categoryName', CategoryName::fromValue($viewData->get('categoryName')));
		}

		return $viewData;
	}
}
