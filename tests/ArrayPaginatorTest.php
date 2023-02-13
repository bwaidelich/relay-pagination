<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests;

use Wwwision\RelayPagination\Loader\ArrayLoader;
use Wwwision\RelayPagination\Paginator;

final class ArrayPaginatorTest extends PaginatorTestBase
{
    public function setUp(): void
    {
        $this->loader = new ArrayLoader(range('a', 'k'));
    }

    protected function renderNode($node): string
    {
        return (string)$node;
    }
}
