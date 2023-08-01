<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Helper;

use App\Application\DataProcessor\Exception\IoException;
use App\Application\DataProcessor\Write\Destination\FileDestination;

final class FilePutContents
{
    private function __construct() {}

    public static function put(FileDestination $destination, string $content): void
    {
        $filename = $destination->filename();
        $chmod = $destination->options()[FileDestination::OPTION_CHMOD] ?? 0666;
        $dir = dirname($filename);

        if (!is_dir($dir) && !@mkdir($dir, $chmod, true) && !is_dir($dir)) {
            throw IoException::unableToCreateDirectory($dir);
        }

        if (false === @file_put_contents($filename, $content)) {
            throw IoException::unableToWriteFile($filename);
        }

        if (!@chmod($filename, $chmod)) {
            throw IoException::unableToChmodFile($filename, $chmod);
        }
    }
}
