<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\ArrayRowData;
use App\Application\DataProcessor\Context\Context;
use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Description\DescriptorInterface;
use App\Application\DataProcessor\Exception\DataReaderExceptionInterface;
use App\Application\DataProcessor\Exception\RowValidationException;
use App\Application\DataProcessor\Exception\StopReadingException;
use App\Application\DataProcessor\Read\Resource\ResourceInterface;
use App\Application\DataProcessor\RowDataInterface;
use App\Application\DataProcessor\RowInterface;
use App\Application\DataProcessor\Write\Resource\ReaderResource;
use App\Application\DataProcessor\Write\Resource\ResourceInterface as WritableResource;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use Nette\Schema\ValidationException;

abstract class AbstractReader implements ReaderInterface
{
    protected function __construct(
        protected ResourceInterface $resource,
    ) {}

    /**
     * @throws DataReaderExceptionInterface
     */
    public function read(?DescriptorInterface $descriptor = null, ?callable $onError = null): iterable
    {
        $processor = new Processor();
        $schema = $descriptor?->schema($this->createContext());
        $errorCallback = ErrorCallback::wrap($onError);

        try {
            foreach ($this->doRead($errorCallback) as $row) {
                if (null !== $schema) {
                    $row = $this->normalizeRow($row, $processor, $schema, $errorCallback);
                }

                if (null !== $row) {
                    yield $row;
                }
            }
        } catch (StopReadingException $e) {
            return;
        }
    }

    public function toWritableResource(?DescriptorInterface $descriptor = null, ?callable $onError = null): WritableResource
    {
        return new ReaderResource($this, $descriptor, $onError);
    }

    /**
     * @return iterable<RowInterface>
     * @throws DataReaderExceptionInterface
     */
    abstract protected function doRead(ErrorCallback $errorCallback): iterable;

    protected function createContext(): ContextInterface
    {
        return Context::default();
    }

    /**
     * @throws DataReaderExceptionInterface
     */
    protected function normalizeRow(RowInterface $row, Processor $processor, Schema $schema, ErrorCallback $errorCallback): ?RowInterface
    {
        try {
            $normalized = $processor->process($schema, $row->data()->toArray());

            if (!$normalized instanceof RowDataInterface) {
                $normalized = ArrayRowData::create((array) $normalized);
            }

            return $row->withData($normalized);
        } catch (ValidationException $e) {
            foreach ($e->getMessages() as $message) {
                $errorCallback(RowValidationException::error($row->index(), $message));
            }
        }

        return null;
    }
}
