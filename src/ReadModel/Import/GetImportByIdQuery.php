<?php

declare(strict_types=1);

namespace App\ReadModel\Import;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ImportView
 */
final class GetImportByIdQuery extends AbstractQuery
{
    /**
     * @return static
     */
    public static function create(string $id): self
    {
        return self::fromParameters([
            'id' => $id,
        ]);
    }

    public function id(): string
    {
        return $this->getParam('id');
    }
}
