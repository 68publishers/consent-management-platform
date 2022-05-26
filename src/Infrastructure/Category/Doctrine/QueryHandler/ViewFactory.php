<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\QueryHandler;

use App\ReadModel\Category\CategoryView;

final class ViewFactory
{
	private function __construct()
	{
	}

	/**
	 * @param array $data
	 *
	 * @return \App\ReadModel\Category\CategoryView
	 */
	public static function createCategoryView(array $data): CategoryView
	{
		$names = [];

		if (isset($data['translations'])) {
			foreach ($data['translations'] as $translation) {
				$names[$translation['locale']->value()] = $translation['name'];
			}

			unset($data['translations']);
		}

		$data['names'] = $names;

		return CategoryView::fromArray($data);
	}
}
