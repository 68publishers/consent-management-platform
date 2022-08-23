<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use App\Application\DataProcessor\Description\Path\Path;
use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Description\Path\PathInfo;

final class ListDescriptor implements DescriptorInterface
{
	private DescriptorInterface $valueDescriptor;

	private function __construct()
	{
	}

	/**
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface $valueDescriptor
	 *
	 * @return static
	 */
	public static function create(DescriptorInterface $valueDescriptor): self
	{
		$descriptor = new self();
		$descriptor->valueDescriptor = $valueDescriptor;

		return $descriptor;
	}

	/**
	 * {@inheritDoc}
	 */
	public function schema(ContextInterface $context): Schema
	{
		$list = Expect::listOf(
			$this->valueDescriptor->schema($context)
		);

		if (TRUE === ($context[ContextInterface::WEAK_TYPES] ?? FALSE)) {
			$list->before(function ($value) {
				if (!is_scalar($value)) {
					return $value;
				}

				$value = trim((string) $value);

				if ('' === $value) {
					return [];
				}

				$value = explode(',', $value);

				return array_map('trim', $value);
			});
		}

		return $list;
	}

	/**
	 * {@inheritDoc}
	 */
	public function pathInfo(Path $path): PathInfo
	{
		$part = $path->shift();
		$pathInfo = new PathInfo();

		if (NULL === $part) {
			$pathInfo->descriptor = $this;
			$pathInfo->found = TRUE;
			$pathInfo->isFinal = FALSE;

			return $pathInfo;
		}

		if (!is_numeric($part)) {
			$pathInfo->descriptor = NULL;
			$pathInfo->found = FALSE;
			$pathInfo->isFinal = FALSE;

			return $pathInfo;
		}

		return $this->valueDescriptor->pathInfo($path);
	}
}
