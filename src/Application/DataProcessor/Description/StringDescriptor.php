<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Type;

final class StringDescriptor extends AbstractTypeDescriptor
{
    protected function createType(ContextInterface $context): Type
    {
        return new Type('string');
    }
}
