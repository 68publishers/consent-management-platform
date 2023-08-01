<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Writer;

use App\Application\DataProcessor\Exception\DataReaderExceptionInterface;
use App\Application\DataProcessor\Exception\WriterException;
use App\Application\DataProcessor\RowInterface;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use Throwable;

abstract class AbstractWriter implements WriterInterface
{
    protected ResourceInterface $resource;

    protected DestinationInterface $destination;

    private bool $locked = false;

    protected function __construct(ResourceInterface $resource, DestinationInterface $destination)
    {
        $this->resource = $resource;
        $this->destination = $destination;
    }

    /**
     * @throws DataReaderExceptionInterface
     */
    public function write(): DestinationInterface
    {
        if ($this->locked) {
            throw WriterException::writerLocked($this->resource, $this->destination);
        }

        $this->locked = true;
        $destination = $this->destination;

        try {
            $this->prepare();

            foreach ($this->resource->rows() as $row) {
                $destination = $this->processRow($row, $destination);
            }

            $destination = $this->finish($destination);
        } catch (DataReaderExceptionInterface $e) {
            throw $e;
        } catch (Throwable $e) {
            throw WriterException::wrap($this->resource, $destination, $e);
        } finally {
            $this->locked = false;
        }

        return $destination;
    }

    abstract protected function processRow(RowInterface $row, DestinationInterface $destination): DestinationInterface;

    abstract protected function finish(DestinationInterface $destination): DestinationInterface;

    protected function prepare(): void
    {
    }
}
