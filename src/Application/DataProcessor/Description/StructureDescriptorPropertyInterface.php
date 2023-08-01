<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Structure;

interface StructureDescriptorPropertyInterface
{
    public function applyToStructure(Structure $structure, ContextInterface $context): Structure;
}
