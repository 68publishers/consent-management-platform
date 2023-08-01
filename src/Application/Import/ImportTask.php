<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\DataProcessor\RowInterface;
use App\Bootstrap;
use Spatie\Async\Task;

final class ImportTask extends Task
{
    /** @var array<RowInterface> */
    private array $rows;

    private ?ImporterInterface $importer;

    /**
     * @param array<RowInterface> $rows
     */
    public function __construct(array $rows, ImporterInterface $importer)
    {
        $this->rows = (static fn (RowInterface ...$rows): array => $rows)(...$rows);
        $this->importer = $importer;
    }

    public function configure(): void
    {
        if (null === $this->importer) {
            $this->importer = Bootstrap::boot()
                ->createContainer()
                ->getByType(ImporterInterface::class);
        }
    }

    public function run(): ImporterResult
    {
        return $this->importer->import($this->rows);
    }

    /**
     * @return array<string>
     */
    public function __sleep(): array
    {
        return ['rows'];
    }
}
