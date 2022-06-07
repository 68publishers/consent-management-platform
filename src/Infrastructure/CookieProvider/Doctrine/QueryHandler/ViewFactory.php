<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\QueryHandler;

use App\ReadModel\CookieProvider\CookieProviderView;

final class ViewFactory
{
	private function __construct()
	{
	}

	/**
	 * @param array $data
	 *
	 * @return \App\ReadModel\CookieProvider\CookieProviderView
	 */
	public static function createCookieProviderView(array $data): CookieProviderView
	{
		$purposes = [];

		if (isset($data['translations'])) {
			foreach ($data['translations'] as $translation) {
				$purposes[$translation['locale']->value()] = $translation['purpose'];
			}

			unset($data['translations']);
		}

		$data['purposes'] = $purposes;

		return CookieProviderView::fromArray($data);
	}
}
