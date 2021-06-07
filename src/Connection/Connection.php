<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

final class Connection implements \IteratorAggregate
{
    private PageInfo $pageInfo;
    private Edges $edges;

    public function __construct(PageInfo $pageInfo, Edges $edges)
    {
        $this->pageInfo = $pageInfo;
        $this->edges = $edges;
    }

    public static function forEmptyResult(): self
    {
        $pageInfo = new PageInfo(false, false, '', '');
        return new self($pageInfo, Edges::empty());
    }

    public function pageInfo(): PageInfo
    {
        return $this->pageInfo;
    }

    public function getIterator(): Edges
    {
        return $this->edges;
    }

    /**
     * @return Edge[]
     */
    public function toArray(): array
    {
        return iterator_to_array($this->edges);
    }

}
