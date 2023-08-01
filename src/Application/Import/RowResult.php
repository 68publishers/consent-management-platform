<?php

declare(strict_types=1);

namespace App\Application\Import;

final class RowResult
{
    private string $rowIndex;

    private bool $ok;

    private string $message;

    private array $warnings = [];

    private function __construct(string $rowIndex, bool $ok, string $message)
    {
        $this->rowIndex = $rowIndex;
        $this->ok = $ok;
        $this->message = $message;
    }

    /**
     * @return static
     */
    public static function success(string $rowIndex, string $message): self
    {
        return new self($rowIndex, true, $message);
    }

    /**
     * @return static
     */
    public static function error(string $rowIndex, string $message): self
    {
        return new self($rowIndex, false, $message);
    }

    /**
     * @return $this
     */
    public function withWarning(string $warning): self
    {
        $result = clone $this;
        $result->warnings[] = $warning;

        return $result;
    }

    public function rowIndex(): string
    {
        return $this->rowIndex;
    }

    public function ok(): bool
    {
        return $this->ok;
    }

    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return string[]
     */
    public function warnings(): array
    {
        return $this->warnings;
    }
}
