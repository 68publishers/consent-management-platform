<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Type;

final class DefaultValue implements TypeDescriptorPropertyInterface
{
    private mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function applyToType(Type $type, ContextInterface $context): Type
    {
        return $type->default($this->value);
    }
}
