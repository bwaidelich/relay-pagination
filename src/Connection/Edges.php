<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

use Webmozart\Assert\Assert;

/**
 * @implements \IteratorAggregate<Edge>
 */
final class Edges implements \IteratorAggregate, \Countable
{

    /**
     * @param Edge[] $edges
     */
    private function __construct(
        private readonly array $edges
    ) {}

    public static function empty(): self
    {
        return new self([]);
    }

    public static function fromArray(array $edges): self
    {
        Assert::allIsInstanceOf($edges, Edge::class);
        return new self($edges);
    }

    public static function fromRawArray(array $array, string $cursorProperty = null): self
    {
        $edges = [];
        foreach ($array as $index => $item) {
            $cursor = $cursorProperty ? $item[$cursorProperty] : $index;
            $edges[] = new Edge((string)$cursor, $item);
        }
        return new self($edges);
    }

    public function reverse(): self
    {
        return new self(array_reverse($this->edges));
    }

    public function skipFirst(): self
    {
        return new self(\array_slice($this->edges, 1));
    }

    public function isEmpty(): bool
    {
        return $this->edges === [];
    }

    public function slice(int $offset, int $length): self
    {
        return new self(\array_slice($this->edges, $offset, $length));
    }

    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->edges));
    }

    public function mapNodes(callable $callback): self
    {
        return new self(array_map(static fn(Edge $edge) => $edge->withNode($callback($edge->node)), $this->edges));
    }

    public function skipLast(): self
    {
        $edges = $this->edges;
        array_pop($edges);
        return new self($edges);
    }

    /**
     * @return \Traversable|Edge[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->edges);
    }

    public function endCursor(): string
    {
        return $this->edges[array_key_last($this->edges)]->cursor;
    }

    public function startCursor(): string
    {
        return $this->edges[array_key_first($this->edges)]->cursor;
    }

    public function count(): int
    {
        return \count($this->edges);
    }

    /**
     * @return Edge[]
     */
    public function toArray(): array
    {
        return array_values($this->edges);
    }
}
