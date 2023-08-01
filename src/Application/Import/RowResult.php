<?php

declare(strict_types=1);

namespace App\Application\Import;

final class RowResult
{
    private array $warnings = [];

    private function __construct(
        private readonly string $rowIndex,
        private readonly bool $ok,
        private readonly string $message,
    ) {}

    public static function success(string $rowIndex, string $message): self
    {
        return new self($rowIndex, true, $message);
    }

    public static function error(string $rowIndex, string $message): self
    {
        return new self($rowIndex, false, $message);
    }

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
     * @return array<string>
     */
    public function warnings(): array
    {
        return $this->warnings;
    }
}
