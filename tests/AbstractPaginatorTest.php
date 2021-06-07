<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests;

use PHPUnit\Framework\TestCase;
use Wwwision\RelayPagination\Connection\Edge;
use Wwwision\RelayPagination\Loader\Loader;
use Wwwision\RelayPagination\Paginator;

abstract class AbstractPaginatorTest extends TestCase
{
    protected ?Loader $loader;

    abstract protected function renderNode($node): string;

    public function test_first_throws_exception_if_numberOfRecords_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $paginator = new Paginator($this->loader);
        $paginator->first(-5);
    }

    public function test_last_throws_exception_if_numberOfRecords_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $paginator = new Paginator($this->loader);
        $paginator->last(-5);
    }

    public function test_forward_pagination(): void
    {
        $nodesPerPage = 3;
        $after = null;
        $actualResult = '';
        $paginator = new Paginator($this->loader);
        do {
            $connection = $paginator->first($nodesPerPage, $after);
            foreach ($connection as $edge) {
                $actualResult .= $this->renderEdge($edge);
            }
            $after = $connection->pageInfo()->endCursor();
            $actualResult .= '.';
        } while ($connection->pageInfo()->hasNextPage());

        self::assertSame('abc.def.ghi.jk.', $actualResult);
    }

    public function test_forward_pagination_reversed(): void
    {
        $nodesPerPage = 3;
        $after = null;
        $actualResult = '';
        $paginator = (new Paginator($this->loader))->reversed();
        do {
            $connection = $paginator->first($nodesPerPage, $after);
            foreach ($connection as $edge) {
                $actualResult .= $this->renderEdge($edge);
            }
            $after = $connection->pageInfo()->endCursor();
            $actualResult .= '.';
        } while ($connection->pageInfo()->hasNextPage());

        self::assertSame('kji.hgf.edc.ba.', $actualResult);
    }

    public function test_forward_pagination_empty_result(): void
    {
        $paginator = new Paginator($this->loader);
        $connection = $paginator->first(5, '999');
        self::assertFalse($connection->pageInfo()->hasPreviousPage());
        self::assertFalse($connection->pageInfo()->hasNextPage());
        self::assertSame('', $connection->pageInfo()->startCursor());
        self::assertSame('', $connection->pageInfo()->endCursor());
        self::assertEmpty($connection->getIterator());
    }

    public function test_backward_pagination(): void
    {
        $nodesPerPage = 3;
        $before = null;
        $actualResult = '';
        $paginator = new Paginator($this->loader);
        do {
            $connection = $paginator->last($nodesPerPage, $before);
            foreach ($connection as $edge) {
                $actualResult .= $this->renderEdge($edge);
            }
            $before = $connection->pageInfo()->endCursor();
            $actualResult .= '.';
        } while ($connection->pageInfo()->hasPreviousPage());

        self::assertSame('ijk.fgh.cde.ab.', $actualResult);
    }

    public function test_backward_pagination_reversed(): void
    {
        $nodesPerPage = 3;
        $before = null;
        $actualResult = '';
        $paginator = (new Paginator($this->loader))->reversed();
        do {
            $connection = $paginator->last($nodesPerPage, $before);
            foreach ($connection as $edge) {
                $actualResult .= $this->renderEdge($edge);
            }
            $before = $connection->pageInfo()->endCursor();
            $actualResult .= '.';
        } while ($connection->pageInfo()->hasPreviousPage());

        self::assertSame('cba.fed.ihg.kj.', $actualResult);
    }

    public function test_backward_pagination_empty_result(): void
    {
        $paginator = new Paginator($this->loader);
        $connection = $paginator->last(5, '999');
        self::assertFalse($connection->pageInfo()->hasPreviousPage());
        self::assertFalse($connection->pageInfo()->hasNextPage());
        self::assertSame('', $connection->pageInfo()->startCursor());
        self::assertSame('', $connection->pageInfo()->endCursor());
        self::assertEmpty($connection->getIterator());
    }
}
