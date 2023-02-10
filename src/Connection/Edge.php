<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

final class Edge
{
    public function __construct(
        private readonly string $cursor,
        private readonly mixed $node
    ) {}

    public function withNode($node): self
    {
        return new self($this->cursor, $node);
    }

    public function cursor(): string
    {
        return $this->cursor;
    }

    public function node(): mixed
    {
        return $this->node;
    }
}
