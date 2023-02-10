<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

final class PageInfo
{
    public function __construct(
        private readonly bool $hasPreviousPage,
        private readonly bool $hasNextPage,
        private readonly string $startCursor,
        private readonly string $endCursor,
    ) {}

    public function hasPreviousPage(): bool
    {
        return $this->hasPreviousPage;
    }

    public function hasNextPage(): bool
    {
        return $this->hasNextPage;
    }

    public function startCursor(): string
    {
        return $this->startCursor;
    }

    public function endCursor(): string
    {
        return $this->endCursor;
    }
}
