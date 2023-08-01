<?php

declare(strict_types=1);

namespace App\ReadModel;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\PaginatedQueryInterface;

interface DataGridQueryInterface extends PaginatedQueryInterface
{
    public const MODE_DATA = 'data';
    public const MODE_ONE = 'one';
    public const MODE_COUNT = 'count';

    public function filters(): array;

    public function sorting(): array;

    public function mode(): string;

    /**
     * @return $this
     */
    public function withFilter(string $name, $value): self;

    /**
     * @return $this
     */
    public function withSorting(string $name, string $direction): self;

    /**
     * @return $this
     */
    public function withDataMode(): self;

    /**
     * @return $this
     */
    public function withOneMode(): self;

    /**
     * @return $this
     */
    public function withCountMode(): self;
}
