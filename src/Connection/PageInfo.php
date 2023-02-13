<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

final class PageInfo
{
    public function __construct(
        public readonly bool $hasPreviousPage,
        public readonly bool $hasNextPage,
        public readonly string $startCursor,
        public readonly string $endCursor,
    ) {}

    /**
     * @deprecated with 1.2 - use public property `PageInfo::hasPreviousPage` instead
     */
    public function hasPreviousPage(): bool
    {
        return $this->hasPreviousPage;
    }

    /**
     * @deprecated with 1.2 - use public property `PageInfo::hasNextPage` instead
     */
    public function hasNextPage(): bool
    {
        return $this->hasNextPage;
    }

    /**
     * @deprecated with 1.2 - use public property `PageInfo::startCursor` instead
     */
    public function startCursor(): string
    {
        return $this->startCursor;
    }

    /**
     * @deprecated with 1.2 - use public property `PageInfo::endCursor` instead
     */
    public function endCursor(): string
    {
        return $this->endCursor;
    }
}
