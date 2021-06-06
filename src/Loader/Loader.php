<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Loader;

use Wwwision\RelayPagination\Connection\Edges;

interface Loader
{
    public function forward(int $limit, string $startCursor = null): Edges;
    public function backward(int $limit, string $endCursor = null): Edges;
}
