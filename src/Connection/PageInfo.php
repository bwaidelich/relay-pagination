<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

final class PageInfo
{
    private bool $hasPreviousPage;
    private bool $hasNextPage;
    private string $startCursor;
    private string $endCursor;

    public function __construct(bool $hasPreviousPage, bool $hasNextPage, string $startCursor, string $endCursor)
    {
        $this->hasPreviousPage = $hasPreviousPage;
        $this->hasNextPage = $hasNextPage;
        $this->startCursor = $startCursor;
        $this->endCursor = $endCursor;
    }

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
