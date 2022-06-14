<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use App\ReadModel\Cookie\CookieView;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataTransformerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class CookieViewDataTransformer implements ViewDataTransformerInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function canTransform(string $viewClassname, ViewDataInterface $viewData): bool
	{
		return is_a($viewClassname, CookieView::class, TRUE) && $viewData instanceof DoctrineViewData;
	}

	/**
	 * {@inheritDoc}
	 */
	public function transform(ViewDataInterface $viewData, ViewFactoryInterface $viewFactory): ViewDataInterface
	{
		$purposes = [];

		if ($viewData->has('translations')) {
			foreach ($viewData->get('translations') as $translation) {
				$purposes[$translation['locale']->value()] = $translation['purpose'];
			}

			$viewData = $viewData->without('translations');
		}

		return $viewData->with('purposes', $purposes);
	}
}
