<?php

declare(strict_types=1);

namespace App\Domain\Cookie\ValueObject;

use DomainException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\ComparableValueObjectInterface;

final class Environment implements ComparableValueObjectInterface
{
    private function __construct(
        private readonly ?string $value,
    ) {}

    public static function fromNative(mixed $native): self
    {
        if (null !== $native && !is_string($native)) {
            throw new DomainException(sprintf(
                'Unable to create %s from a native. The native must be a string or null.',
                self::class,
            ));
        }

        return new self($native);
    }

    public static function fromSafeNative(mixed $native): self
    {
        assert(null === $native || is_string($native));

        return new self($native);
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function equals(ComparableValueObjectInterface $valueObject): bool
    {
        return $valueObject instanceof $this && $valueObject->value() === $this->value();
    }
}
