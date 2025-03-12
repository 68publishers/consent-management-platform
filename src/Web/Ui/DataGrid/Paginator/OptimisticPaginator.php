<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Paginator;

use Nette\Utils\Paginator;

class OptimisticPaginator extends Paginator
{
    private int $base = 1;

    /** @var positive-int */
    private int $itemsPerPage = 1;

    private int $page = 1;

    /** @var int<0, max>|null */
    private ?int $itemCount = null;

    public function setPage(int $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getPage(): int
    {
        return $this->base + $this->getPageIndex();
    }

    public function getFirstPage(): int
    {
        return $this->base;
    }

    public function getLastPage(): ?int
    {
        return $this->itemCount === null
            ? null
            : $this->base + max(0, $this->getPageCount() - 1);
    }

    public function getFirstItemOnPage(): int
    {
        return $this->itemCount !== 0
            ? $this->offset + 1
            : 0;
    }

    public function getLastItemOnPage(): int
    {
        return $this->offset + $this->length;
    }

    public function setBase(int $base): static
    {
        $this->base = $base;

        return $this;
    }

    public function getBase(): int
    {
        return $this->base;
    }

    protected function getPageIndex(): int
    {
        return max(0, $this->page - $this->base);
    }

    public function isFirst(): bool
    {
        return $this->getPageIndex() === 0;
    }

    public function isLast(): bool
    {
        return !($this->itemCount === null) && $this->getPageIndex() >= $this->getPageCount() - 1;
    }

    public function getPageCount(): ?int
    {
        return $this->itemCount === null
            ? null
            : (int) ceil($this->itemCount / $this->itemsPerPage);
    }

    public function setItemsPerPage(int $itemsPerPage): static
    {
        $this->itemsPerPage = max(1, $itemsPerPage);

        return $this;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setItemCount(?int $itemCount = null): static
    {
        $this->itemCount = $itemCount === null ? null : max(0, $itemCount);

        return $this;
    }

    public function getItemCount(): ?int
    {
        return $this->itemCount;
    }

    public function getOffset(): int
    {
        return $this->getPageIndex() * $this->itemsPerPage;
    }

    public function getCountdownOffset(): ?int
    {
        return $this->itemCount === null
            ? null
            : max(0, $this->itemCount - ($this->getPageIndex() + 1) * $this->itemsPerPage);
    }

    public function getLength(): int
    {
        return $this->itemCount === null
            ? $this->itemsPerPage
            : min($this->itemsPerPage, $this->itemCount - $this->getPageIndex() * $this->itemsPerPage);
    }
}
