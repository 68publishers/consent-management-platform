<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Structure;

final class AllowOthers implements StructureDescriptorPropertyInterface
{
    public function applyToStructure(Structure $structure, ContextInterface $context): Structure
    {
        return $structure->otherItems();
    }
}
