<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\ArrayRowData;
use App\Application\DataProcessor\Exception\ReaderException;
use App\Application\DataProcessor\Read\Resource\QueryResource;
use App\Application\DataProcessor\Row;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractArrayValueObject;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractEnumValueObject;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractIntegerValueObject;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractStringValueObject;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractUuidIdentity;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractValueObjectSet;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch;
use Throwable;

final class QueryReader extends AbstractReader
{
    private QueryBusInterface $queryBus;

    protected function __construct(QueryBusInterface $queryBus, QueryResource $resource)
    {
        parent::__construct($resource);

        $this->queryBus = $queryBus;
    }

    public static function create(QueryBusInterface $queryBus, QueryResource $resource): self
    {
        return new self($queryBus, $resource);
    }

    protected function doRead(ErrorCallback $errorCallback): iterable
    {
        $resource = $this->resource;
        assert($resource instanceof QueryResource);

        $query = $resource->query();

        try {
            $i = 0;

            foreach ($this->queryBus->dispatch($query) as $row) {
                if (!$row instanceof Batch) {
                    yield $this->createRow($i, (array) $row);

                    $i++;

                    continue;
                }

                foreach ($row->results() as $result) {
                    yield $this->createRow($i, (array) $result);

                    $i++;
                }
            }
        } catch (Throwable $e) {
            $errorCallback(ReaderException::invalidResource($e->getMessage()));
        }
    }

    private function createRow(int $index, array $row): Row
    {
        return Row::create(
            (string) $index,
            ArrayRowData::create($this->valueObjects2primitives($row)),
        );
    }

    private function valueObjects2primitives(array $row): array
    {
        foreach ($row as $key => $value) {
            if ($value instanceof AbstractStringValueObject || $value instanceof AbstractIntegerValueObject || $value instanceof AbstractEnumValueObject) {
                $row[$key] = $value->value();

                continue;
            }

            if ($value instanceof AbstractArrayValueObject) {
                $row[$key] = $value->values();

                continue;
            }

            if ($value instanceof AbstractUuidIdentity) {
                $row[$key] = $value->toString();

                continue;
            }

            if ($value instanceof AbstractValueObjectSet) {
                $row[$key] = $value->toArray();

                continue;
            }

            if (is_array($value)) {
                $row[$key] = $this->valueObjects2primitives($value);
            }
        }

        return $row;
    }
}
