<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Type;

final class Pattern implements TypeDescriptorPropertyInterface
{
    private string $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function applyToType(Type $type, ContextInterface $context): Type
    {
        return $type->pattern($this->pattern);
    }
}
