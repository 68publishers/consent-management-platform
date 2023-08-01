<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

final class GetProjectTemplateByCodeAndLocaleWithFallbackQuery extends AbstractQuery
{
    public static function create(string $code, ?string $locale = null): self
    {
        return self::fromParameters([
            'code' => $code,
            'locale' => $locale,
        ]);
    }

    public function code(): string
    {
        return $this->getParam('code');
    }

    public function locale(): ?string
    {
        return $this->getParam('locale');
    }
}
