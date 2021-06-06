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

    public function forward(int $limit, string $startCursor = null): Edges
    {
        $offset = $this->extractOffsetFromCursor($startCursor);
        return Edges::fromRawArray(
            $this->base64encodeArrayKeys(
                \array_slice($this->array, $offset, $limit, true)
            )
        );
    }

    public function backward(int $limit, string $endCursor = null): Edges
    {
        $offset = $this->extractOffsetFromCursor($endCursor);
        return Edges::fromRawArray(
            $this->base64encodeArrayKeys(
                \array_slice(\array_reverse($this->array, true), -$offset - 1, $limit, true)
            )
        );
    }

    private function extractOffsetFromCursor(string $cursor = null): int
    {
        if ($cursor === null) {
            return 0;
        }
        return (int)base64_decode($cursor);
    }

    private function base64encodeArrayKeys(array $array): array
    {
        return array_merge(...array_map(static fn($key, $value) => [base64_encode((string)$key) => $value], array_keys($array), $array));
    }
}
