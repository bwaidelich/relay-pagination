<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

use Wwwision\RelayPagination\Loader\Loader;

final class ForwardConnection implements Connection
{
    private PageInfo $pageInfo;
    private Edges $edges;

    public function __construct(Loader $loader, int $first, string $after = null)
    {
        $hasPreviousPage = false;
        $hasNextPage = false;
        try {
            $edges = $loader->forward($first + 2, $after);
        } catch (\Throwable $exception) {
            throw new \InvalidArgumentException(sprintf('Failed to load first %d nodes after cursor %s', $first, $after ?? '(not specified)'), 1622912403, $exception);
        }
        if ($after !== null && $edges->startCursor() === $after) {
            $hasPreviousPage = true;
            $edges = $edges->skipFirst();
        }
        if ($edges->count() > $first) {
            $hasNextPage = true;
            $edges = $edges->slice($first);
        }
        $this->pageInfo = new PageInfo($hasPreviousPage, $hasNextPage, $edges->startCursor(), $edges->endCursor());
        $this->edges = $edges;
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
