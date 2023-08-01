<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Resource;

use JsonException;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryInterface;

final class QueryResource implements ResourceInterface
{
    private QueryInterface $query;

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(QueryInterface $query): self
    {
        $resource = new self();
        $resource->query = $query;

        return $resource;
    }

    public function query(): QueryInterface
    {
        return $this->query;
    }

    public function options(): array
    {
        return [];
    }

    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        return sprintf(
            'QUERY(%s, %s)',
            get_class($this->query),
            json_encode($this->query->parameters(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        );
    }
}
