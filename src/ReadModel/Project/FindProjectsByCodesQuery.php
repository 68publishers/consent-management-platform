<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

final class FindProjectsByCodesQuery extends AbstractQuery
{
    /**
     * @param array<string> $codes
     */
    public static function create(array $codes): self
    {
        return self::fromParameters([
            'codes' => $codes,
        ]);
    }

    /**
     * @return array<string>
     */
    public function codes(): array
    {
        return $this->getParam('codes');
    }
}
