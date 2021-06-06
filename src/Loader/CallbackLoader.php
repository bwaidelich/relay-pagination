<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Loader;

use Wwwision\RelayPagination\Connection\Edges;

/**
 * Loader that invokes closures to determine pagination results.
 *
 * Note: This loader is mainly meant for rapid development, testing & debugging purposes.
 * Usually you'd want to create a custom implementation of the Loader interface instead
 *
 * Usage:
 *
 * $loader = new CallbackLoader(
 *   fn(int $limit, string $startCursor = null) => Edges::fromRawArray(....),
 *   fn(int $limit, string $endCursor = null) => Edges::fromRawArray(....)
 * );
 */
final class CallbackLoader implements Loader
{
    private \Closure $forwardCallback;
    private \Closure $backwardCallback;

    public function __construct(\Closure $forwardCallback, \Closure $backwardCallback)
    {
        $this->forwardCallback = $forwardCallback;
        $this->backwardCallback = $backwardCallback;
    }

    public function forward(int $limit, string $startCursor = null): Edges
    {
        return \call_user_func($this->forwardCallback, $limit, $startCursor);
    }

    public function backward(int $limit, string $endCursor = null): Edges
    {
        return \call_user_func($this->backwardCallback, $limit, $endCursor);
    }
}
