<?php

declare(strict_types=1);

namespace App\Bridge\Latte;

use RuntimeException;
use Nette\Utils\Validators;
use Psr\Log\LoggerInterface;
use SixtyEightPublishers\WebpackEncoreBundle\Asset\EntryPointLookupCollectionInterface;

final class InternalCssRenderer
{
	private string $publicDir;

	private bool $debugMode;

	private EntryPointLookupCollectionInterface $entryPointLookupCollection;

	private LoggerInterface $logger;

	public function __construct(string $publicDir, bool $debugMode, EntryPointLookupCollectionInterface $entryPointLookupCollection, LoggerInterface $logger)
	{
		$this->publicDir = rtrim($publicDir, DIRECTORY_SEPARATOR);
		$this->debugMode = $debugMode;
		$this->entryPointLookupCollection = $entryPointLookupCollection;
		$this->logger = $logger;
	}

	/**
	 * @param string      $entryName
	 * @param string|NULL $buildName
	 *
	 * @return string
	 */
	public function render(string $entryName, ?string $buildName = NULL): string
	{
		$entryPointLookup = $this->entryPointLookupCollection->getEntryPointLookup($buildName);
		$styles = [];

		foreach ($entryPointLookup->getCssFiles($entryName) as $file) {
			if (!Validators::isUrl($file)) {
				$file = sprintf(
					'%s/%s',
					$this->publicDir,
					ltrim($file, DIRECTORY_SEPARATOR)
				);
			}

			$content = @file_get_contents($file);

			if (FALSE !== $content) {
				$styles[] = $content;

				continue;
			}

			$errorMessage = sprintf(
				'Can\'t include internal css from file %s. File not found or not readable.',
				$file
			);

			if ($this->debugMode) {
				throw new RuntimeException($errorMessage);
			}

			$this->logger->error($errorMessage);
		}

		return sprintf(
			"<style>\n%s\n</style>",
			implode("\n", $styles)
		);
	}
}
