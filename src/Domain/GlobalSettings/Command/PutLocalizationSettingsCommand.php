<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class PutLocalizationSettingsCommand extends AbstractCommand
{
    /**
     * @return static
     */
    public static function create(array $locales, string $defaultLocale): self
    {
        return self::fromParameters([
            'locales' => $locales,
            'default_locale' => $defaultLocale,
        ]);
    }

    public function locales(): array
    {
        return $this->getParam('locales');
    }

    public function defaultLocale(): string
    {
        return $this->getParam('default_locale');
    }
}
