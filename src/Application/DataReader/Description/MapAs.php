<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Elements\Structure;
use App\Application\DataReader\Context\ContextInterface;

final class MapAs implements StructureDescriptorPropertyInterface
{
	private string $classname;

	/**
	 * @param string $classname
	 */
	public function __construct(string $classname)
	{
		$this->classname = $classname;
	}

	/**
	 * @param \Nette\Schema\Elements\Structure                     $structure
	 * @param \App\Application\DataReader\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Elements\Structure
	 */
	public function applyToStructure(Structure $structure, ContextInterface $context): Structure
	{
		return $structure->castTo($this->classname);
	}
}
