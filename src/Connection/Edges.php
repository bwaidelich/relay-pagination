<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

use Webmozart\Assert\Assert;

final class Edges implements \IteratorAggregate, \Countable
{
    /**
     * @var Edge[]
     */
    private array $edges;

    private function __construct(array $edges)
    {
        if ($edges === []) {
            throw new \InvalidArgumentException('Empty edges', 1622912524);
        }
        $this->edges = $edges;
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
            $cursor = $cursorProperty ? $item[$cursorProperty] : (string)$index;
            $edges[] = new Edge($cursor, $item);
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

    public function slice(int $length): self
    {
        return new self(\array_slice($this->edges, 0, $length));
    }

    public function skipLast(): self
    {
        $edges = $this->edges;
        array_pop($edges);
        return new self($edges);
    }

    /**
     * @return iterable|Edge[]
     */
    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->edges);
    }

    public function endCursor(): string
    {
        return $this->edges[array_key_last($this->edges)]->cursor();
    }

    public function startCursor(): string
    {
        return $this->edges[array_key_first($this->edges)]->cursor();
    }

    public function count(): int
    {
        return count($this->edges);
    }
}
