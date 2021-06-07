<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination;

use Wwwision\RelayPagination\Connection\Connection;
use Wwwision\RelayPagination\Connection\PageInfo;
use Wwwision\RelayPagination\Loader\Loader;

/**
 * The main authority of this package allows to paginate through results.
 *
 * Usage:
 *
 * $paginator = new Paginator($loader);
 * $firstPage = $paginator->first(10);
 * foreach ($firstPage as $edge) {
 *   // $edge->cursor()
 *   // $edge->node();
 * }
 * if ($firstPage->pageInfo()->hasNextPage()) {
 *   $secondPage = $paginator->first(10, $firstPage->pageInfo()->endCursor());
 * }
 * // ...
 *
 */
final class Paginator
{
    private Loader $loader;
    private bool $reverse;

    /**
     * @param Loader $loader The loader implementation that is responsible for fetching results from arbitrary data sources
     * @param bool $reverse If true the order of all edges is reversed (abc.def => fed.cba). This allows to paginate through results in descending order
     */
    public function __construct(Loader $loader, bool $reverse = false)
    {
        $this->loader = $loader;
        $this->reverse = $reverse;
    }

    public function first(int $numberOfRecords, string $after = null): Connection
    {
        if ($this->reverse) {
            return $this->backwardConnection($numberOfRecords, $after, true);
        }
        return $this->forwardConnection($numberOfRecords, $after);
    }

    public function last(int $numberOfRecords, string $before = null): Connection
    {
        if ($this->reverse) {
            return $this->forwardConnection($numberOfRecords, $before, true);
        }
        return $this->backwardConnection($numberOfRecords, $before);
    }

    private function forwardConnection(int $first, string $after = null, bool $reverse = false): Connection
    {
        $hasPreviousPage = false;
        $hasNextPage = false;
        try {
            $edges = $this->loader->first($first + 2, $after);
        } catch (\Throwable $exception) {
            throw new \InvalidArgumentException(sprintf('Failed to load first %d nodes from cursor %s', $first, $after ?? '(not specified)'), 1622912403, $exception);
        }
        if ($edges->isEmpty() || ($after !== null && $edges->startCursor() !== $after)) {
            return Connection::forEmptyResult();
        }
        if ($after !== null) {
            $hasPreviousPage = true;
            $edges = $edges->skipFirst();
        }
        if ($edges->count() > $first) {
            $hasNextPage = true;
            $edges = $edges->slice(0, $first);
        }
        if ($reverse) {
            $pageInfo = new PageInfo($hasNextPage, $hasPreviousPage, $edges->startCursor(), $edges->endCursor());
            $edges = $edges->reverse();
        } else {
            $pageInfo = new PageInfo($hasPreviousPage, $hasNextPage, $edges->startCursor(), $edges->endCursor());
        }
        return new Connection($pageInfo, $edges);
    }

    private function backwardConnection(int $last, string $before = null, bool $reverse = false): Connection
    {
        $hasPreviousPage = false;
        $hasNextPage = false;
        try {
            $edges = $this->loader->last($last + 2, $before);
        } catch (\Throwable $exception) {
            throw new \InvalidArgumentException(sprintf('Failed to load last %d nodes until cursor %s', $last, $before ?? '(not specified)'), 1622912463, $exception);
        }
        if ($edges->isEmpty() || ($before !== null && $edges->endCursor() !== $before)) {
            return Connection::forEmptyResult();
        }
        if ($before !== null) {
            $hasNextPage = true;
            $edges = $edges->skipLast();
        }
        if ($edges->count() > $last) {
            $hasPreviousPage = true;
            $edges = $edges->slice(-$last, $last);
        }
        if ($reverse) {
            $pageInfo = new PageInfo($hasNextPage, $hasPreviousPage, $edges->endCursor(), $edges->startCursor());
            $edges = $edges->reverse();
        } else {
            $pageInfo = new PageInfo($hasPreviousPage, $hasNextPage, $edges->endCursor(), $edges->startCursor());
        }
        return new Connection($pageInfo, $edges);
    }
}
