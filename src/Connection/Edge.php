<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

final class Edge
{
    public function __construct(
        public readonly string $cursor,
        public readonly mixed $node
    ) {}

    public function withNode($node): self
    {
        return new self($this->cursor, $node);
    }

    /**
     * @deprecated with 1.2 - use public property `Edge::cursor` instead
     */
    public function cursor(): string
    {
        return $this->cursor;
    }

    /**
     * @deprecated with 1.2 - use public property `Edge::node` instead
     */
    public function node(): mixed
    {
        return $this->node;
    }
}
