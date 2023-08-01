<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Helper;

use App\Web\Utils\TranslatorUtils;
use Nette\Localization\Translator;
use Nette\StaticClass;

final class FilterHelper
{
    use StaticClass;

    public static function all(Translator $translator): array
    {
        return [
            '' => $translator->translate('ublaboo_datagrid.all'),
        ];
    }

    public static function bool(Translator $translator): array
    {
        return self::all($translator) + [
            1 => $translator->translate('ublaboo_datagrid.boolean_filter.yes'),
            0 => $translator->translate('ublaboo_datagrid.boolean_filter.no'),
        ];
    }

    public static function select(array $enum, bool $preserveKeys, Translator $translator, ?string $translatorPrefix = null): array
    {
        return self::all($translator) + self::items($enum, $preserveKeys, $translator, $translatorPrefix);
    }

    public static function items(array $enum, bool $preserveKeys = false, ?Translator $translator = null, ?string $translatorPrefix = null): array
    {
        $items = $preserveKeys ? $enum : array_combine($enum, $enum);

        if (null === $translator || null === $translatorPrefix) {
            return $items;
        }

        return TranslatorUtils::translateArray($translator, $translatorPrefix, $items);
    }
}
