<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Loader;

use Wwwision\RelayPagination\Connection\Edges;

/**
 * The ArrayLoader allows to paginate arbitrary arrays
 *
 * Note: This loader is mainly meant for testing and debugging purposes and it should not be used for large datasets
 * because the whole array has to be loaded into memory, obviously.
 */
final class ArrayLoader implements Loader
{
    private array $array;

    public function __construct(array $array)
    {
        $this->array = array_values($array);
    }

    public function first(int $limit, string $startCursor = null): Edges
    {
        if ($startCursor !== null && !is_numeric($startCursor)) {
            throw new \InvalidArgumentException(sprintf('Invalid cursor "%s", only numeric cursors are supported by the ArrayLoader', $startCursor), 1622981129);
        }
        $offset = $startCursor !== null ? (int)$startCursor : 0;
        return Edges::fromRawArray(\array_slice($this->array, $offset, $limit, true));
    }

    public function last(int $limit, string $endCursor = null): Edges
    {
        if ($endCursor !== null && !is_numeric($endCursor)) {
            throw new \InvalidArgumentException(sprintf('Invalid cursor "%s", only numeric cursors are supported by the ArrayLoader', $endCursor), 1622984163);
        }
        $offset = $endCursor !== null ? max(0, min((int)$endCursor, \count($this->array) - 1) - $limit + 1) : -$limit;
        $length = $endCursor !== null ? min($limit, (int)$endCursor + 1) : $limit;
        return Edges::fromRawArray(\array_slice($this->array, $offset, $length, true));
    }
}
