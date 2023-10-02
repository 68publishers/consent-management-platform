<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\ValueObject;

use App\Domain\GlobalSettings\Exception\UnableToCreateEnvironmentFromNativeValue;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\ComparableValueObjectInterface;

final class Environment implements ComparableValueObjectInterface
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly Color $color,
    ) {}

    public static function fromSafeNative(mixed $native): self
    {
        assert(is_array($native) && isset($native['code']) && isset($native['name']) && isset($native['color']));

        return new Environment(
            code: $native['code'],
            name: $native['name'],
            color: Color::fromValue($native['color']),
        );
    }

    public static function fromNative(mixed $native): self
    {
        if (!is_array($native)) {
            throw UnableToCreateEnvironmentFromNativeValue::nativeMustBeArray();
        }

        foreach (['code', 'name', 'color'] as $key) {
            if (!isset($native[$key])) {
                throw UnableToCreateEnvironmentFromNativeValue::missingNativeKey($key);
            }

            if (!is_string($native[$key])) {
                throw UnableToCreateEnvironmentFromNativeValue::invalidNativeValueType($key, 'a string');
            }
        }

        return new Environment(
            code: $native['code'],
            name: $native['name'],
            color: Color::fromValidColor($native['color']),
        );
    }

    public function equals(ComparableValueObjectInterface $valueObject): bool
    {
        return $valueObject instanceof self
            && $valueObject->code === $this->code
            && $valueObject->name === $this->name
            && $valueObject->color->equals($this->color);
    }

    /**
     * @return array{
     *     code: string,
     *     name: string,
     *     color: string,
     * }
     */
    public function toNative(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'color' => $this->color->value(),
        ];
    }
}
