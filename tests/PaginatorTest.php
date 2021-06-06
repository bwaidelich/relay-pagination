<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests;

use PHPUnit\Framework\TestCase;
use Wwwision\RelayPagination\Loader\ArrayLoader;
use Wwwision\RelayPagination\Paginator;

final class PaginatorTest extends TestCase
{
    private ?Paginator $paginator;

    public function setUp(): void
    {
        $loader = new ArrayLoader(range('a', 'k'));
        $this->paginator = new Paginator($loader);
    }

    public function test_forward_pagination(): void
    {
        $nodesPerPage = 3;
        $after = null;
        $actualResult = '';
        do {
            $connection = $this->paginator->first($nodesPerPage, $after);
            foreach ($connection as $edge) {
                $actualResult .= $edge->node();
            }
            $after = $connection->pageInfo()->endCursor();
            $actualResult .= '.';
        } while ($connection->pageInfo()->hasNextPage());

        self::assertSame('abc.def.ghi.jk.', $actualResult);
    }

    public function test_backward_pagination(): void
    {
        $nodesPerPage = 3;
        $before = null;
        $actualResult = '';
        do {
            $connection = $this->paginator->last($nodesPerPage, $before);
            foreach ($connection as $edge) {
                $actualResult .= $edge->node();
            }
            $before = $connection->pageInfo()->endCursor();
            $actualResult .= '.';
        } while ($connection->pageInfo()->hasPreviousPage());

        self::assertSame('ijk.fgh.cde.ab.', $actualResult);
    }
}
