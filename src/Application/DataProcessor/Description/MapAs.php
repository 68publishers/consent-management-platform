<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use App\Application\DataProcessor\Context\ContextInterface;
use Nette\Schema\Elements\Structure;

final class MapAs implements StructureDescriptorPropertyInterface
{
    private string $classname;

    public function __construct(string $classname)
    {
        $this->classname = $classname;
    }

    public function applyToStructure(Structure $structure, ContextInterface $context): Structure
    {
        return $structure->castTo($this->classname);
    }
}
