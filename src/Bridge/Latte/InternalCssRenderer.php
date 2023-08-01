<?php

declare(strict_types=1);

namespace App\Bridge\Latte;

use Nette\Utils\Validators;
use Psr\Log\LoggerInterface;
use RuntimeException;
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

    public function render(string $entryName, ?string $buildName = null): string
    {
        $entryPointLookup = $this->entryPointLookupCollection->getEntryPointLookup($buildName);
        $styles = [];

        foreach ($entryPointLookup->getCssFiles($entryName) as $file) {
            if (!Validators::isUrl($file)) {
                $file = sprintf(
                    '%s/%s',
                    $this->publicDir,
                    ltrim($file, DIRECTORY_SEPARATOR),
                );
            }

            $content = @file_get_contents($file);

            if (false !== $content) {
                $styles[] = $content;

                continue;
            }

            $errorMessage = sprintf(
                'Can\'t include internal css from file %s. File not found or not readable.',
                $file,
            );

            if ($this->debugMode) {
                throw new RuntimeException($errorMessage);
            }

            $this->logger->error($errorMessage);
        }

        return sprintf(
            "<style>\n%s\n</style>",
            implode("\n", $styles),
        );
    }
}
