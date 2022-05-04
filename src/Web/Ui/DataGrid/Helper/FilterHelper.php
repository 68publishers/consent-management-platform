<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Helper;

use Nette\StaticClass;
use App\Web\Utils\TranslatorUtils;
use Nette\Localization\Translator;

final class FilterHelper
{
	use StaticClass;

	/**
	 * @param \Nette\Localization\Translator $translator
	 *
	 * @return array
	 */
	public static function all(Translator $translator): array
	{
		return [
			'' => $translator->translate('ublaboo_datagrid.all'),
		];
	}

	/**
	 * @param \Nette\Localization\Translator $translator
	 *
	 * @return array
	 */
	public static function bool(Translator $translator): array
	{
		return self::all($translator) + [
			1 => $translator->translate('ublaboo_datagrid.boolean_filter.yes'),
			0 => $translator->translate('ublaboo_datagrid.boolean_filter.no'),
		];
	}

	/**
	 * @param \Nette\Localization\Translator $translator
	 * @param array                          $enum
	 * @param bool                           $preserveKeys
	 * @param string|NULL                    $translatorPrefix
	 *
	 * @return array
	 */
	public static function select(Translator $translator, array $enum, bool $preserveKeys = FALSE, ?string $translatorPrefix = NULL): array
	{
		return self::all($translator) + self::items($enum, $preserveKeys, $translator, $translatorPrefix);
	}

	/**
	 * @param array                               $enum
	 * @param bool                                $preserveKeys
	 * @param \Nette\Localization\Translator|NULL $translator
	 * @param string|NULL                         $translatorPrefix
	 *
	 * @return array
	 */
	public static function items(array $enum, bool $preserveKeys = FALSE, ?Translator $translator = NULL, ?string $translatorPrefix = NULL): array
	{
		$items = $preserveKeys ? $enum : array_combine($enum, $enum);

		if (NULL === $translator || NULL === $translatorPrefix) {
			return $items;
		}

		return TranslatorUtils::translateArray($translator, $translatorPrefix, $items);
	}
}
