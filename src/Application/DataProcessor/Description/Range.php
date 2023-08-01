<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Type;

final class Range implements TypeDescriptorPropertyInterface
{
    private array $range;

    public function __construct(?float $min, ?float $max)
    {
        $this->range = [$min, $max];
    }

    public function applyToType(Type $type, ContextInterface $context): Type
    {
        return $type
            ->min($this->range[0])
            ->max($this->range[1]);
    }
}
