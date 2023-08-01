<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Context;

use InvalidArgumentException;

final class Context implements ContextInterface
{
    private array $context = [];

    private function __construct() {}

    public static function default(array $array = []): ContextInterface
    {
        return self::fromArray(array_merge([
            self::WEAK_TYPES => false,
        ], $array));
    }

    public static function fromArray(array $array): ContextInterface
    {
        $context = new self();
        $context->context = $array;

        return $context;
    }

    public function offsetExists($offset): bool
    {
        return $this->exists($offset, false);
    }

    public function offsetGet($offset): mixed
    {
        $this->exists($offset);

        return $this->context[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->context[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        $this->exists($offset);

        unset($this->context[$offset]);
    }

    /**
     * @param mixed $offset
     */
    private function exists(string $offset, bool $throw = true): bool
    {
        $exists = array_key_exists($offset, $this->context);

        if (!$exists && $throw) {
            throw new InvalidArgumentException(sprintf(
                'Missing context options %s.',
                $offset,
            ));
        }

        return $exists;
    }
}
