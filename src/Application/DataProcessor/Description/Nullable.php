<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Type;

final class Nullable implements TypeDescriptorPropertyInterface
{
    public function applyToType(Type $type, ContextInterface $context): Type
    {
        return $type->nullable();
    }
}
