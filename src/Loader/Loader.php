<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Loader;

use Wwwision\RelayPagination\Connection\Edges;

interface Loader
{
    public function first(int $limit, string $startCursor = null): Edges;
    public function last(int $limit, string $endCursor = null): Edges;
}
