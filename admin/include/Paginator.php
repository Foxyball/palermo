<?php

class Paginator
{
    public int $perPage;
    public int $currentPage;
    public int $totalItems;
    public int $totalPages;

    public function __construct(int $totalItems, int $currentPage = 1, int $perPage = 5)
    {
        $this->perPage = max(1, $perPage);
        $this->totalItems = max(0, $totalItems);
        $this->totalPages = (int) max(1, ceil($this->totalItems / $this->perPage));
        $this->currentPage = min(max(1, $currentPage), $this->totalPages);
    }

    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function limit(): int
    {
        return $this->perPage;
    }

    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    public function pages(): array
    {
        // Simple pages list (no windowing required for small dataset)
        return range(1, $this->totalPages);
    }
}
