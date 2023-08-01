<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Exception;

use RuntimeException;

final class IoException extends RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * @return static
     */
    public static function fileNotFound(string $filename): self
    {
        return new self(sprintf(
            'File %s not found',
            $filename,
        ));
    }

    /**
     * @return static
     */
    public static function fileNotReadable(string $filename): self
    {
        return new self(sprintf(
            'Unable to read file %s',
            $filename,
        ));
    }

    /**
     * @return static
     */
    public static function unableToCreateDirectory(string $directory): self
    {
        return new self(sprintf(
            'Unable to create a directory "%s".',
            $directory,
        ));
    }

    /**
     * @return static
     */
    public static function unableToWriteFile(string $filename): self
    {
        return new self(sprintf(
            'Unable to write a file "%s".',
            $filename,
        ));
    }

    /**
     * @return static
     */
    public static function unableToChmodFile(string $filename, int $chmod): self
    {
        return new self(sprintf(
            'Unable to chmod a file "%s" to mode %s.',
            $filename,
            decoct($chmod),
        ));
    }
}
