<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests;

use Wwwision\RelayPagination\Connection\Edge;
use Wwwision\RelayPagination\Loader\ArrayLoader;

final class ArrayPaginatorTest extends AbstractPaginatorTest
{
    public function setUp(): void
    {
        $this->loader = new ArrayLoader(range('a', 'k'));
    }

    protected function renderEdge(Edge $edge): string
    {
        return (string)$edge->node();
    }
}
