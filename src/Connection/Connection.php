<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

interface Connection extends \IteratorAggregate
{
    public function pageInfo(): PageInfo;

    public function getIterator(): Edges;
}
