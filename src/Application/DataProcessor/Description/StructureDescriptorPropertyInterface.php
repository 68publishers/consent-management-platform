<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Elements\Structure;
use App\Application\DataProcessor\Context\ContextInterface;

interface StructureDescriptorPropertyInterface
{
	/**
	 * @param \Nette\Schema\Elements\Structure                        $structure
	 * @param \App\Application\DataProcessor\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Elements\Structure
	 */
	public function applyToStructure(Structure $structure, ContextInterface $context): Structure;
}
