<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\ReadModel;

use App\ReadModel\CookieProvider\CookieProviderView;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataTransformerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class CookieProviderViewDataTransformer implements ViewDataTransformerInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function canTransform(string $viewClassname, ViewDataInterface $viewData): bool
	{
		return is_a($viewClassname, CookieProviderView::class, TRUE) && $viewData instanceof DoctrineViewData;
	}

	/**
	 * {@inheritDoc}
	 */
	public function transform(ViewDataInterface $viewData): ViewDataInterface
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
