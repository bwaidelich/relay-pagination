<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination;

use Wwwision\RelayPagination\Connection\BackwardConnection;
use Wwwision\RelayPagination\Connection\Connection;
use Wwwision\RelayPagination\Connection\ForwardConnection;
use Wwwision\RelayPagination\Loader\Loader;

final class Paginator
{
    private Loader $loader;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    public function first(int $numberOfRecords, string $after = null): Connection
    {
        return new ForwardConnection($this->loader, $numberOfRecords, $after);
    }

    public function last(int $numberOfRecords, string $before = null): Connection
    {
        return new BackwardConnection($this->loader, $numberOfRecords, $before);
    }
}
