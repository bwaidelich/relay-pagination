<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

use Wwwision\RelayPagination\Loader\Loader;

final class BackwardConnection implements Connection
{
    private PageInfo $pageInfo;
    private Edges $edges;

    public function __construct(Loader $loader, int $last, string $before = null)
    {
        $hasPreviousPage = false;
        $hasNextPage = false;
        try {
            $edges = $loader->backward($last + 2, $before);
        } catch (\Throwable $exception) {
            throw new \InvalidArgumentException(sprintf('Failed to load last %d nodes before cursor %s', $last, $before ?? '(not specified)'), 1622912463, $exception);
        }
        if ($before !== null && $edges->startCursor() === $before) {
            $hasNextPage = true;
            $edges = $edges->skipFirst();
        }
        if ($edges->count() > $last) {
            $hasPreviousPage = true;
            $edges = $edges->slice($last);
        }
        $this->pageInfo = new PageInfo($hasPreviousPage, $hasNextPage, $edges->endCursor(), $edges->startCursor());
        $this->edges = $edges->reverse();
    }

    public function pageInfo(): PageInfo
    {
        return $this->pageInfo;
    }

    public function getIterator(): Edges
    {
        return $this->edges;
    }
}
