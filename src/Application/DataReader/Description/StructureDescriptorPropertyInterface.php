<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Elements\Structure;
use App\Application\DataReader\Context\ContextInterface;

interface StructureDescriptorPropertyInterface
{
	/**
	 * @param \Nette\Schema\Elements\Structure                     $structure
	 * @param \App\Application\DataReader\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Elements\Structure
	 */
	public function applyToStructure(Structure $structure, ContextInterface $context): Structure;
}
