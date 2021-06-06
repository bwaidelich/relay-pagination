<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Connection;

final class Edge
{
    private string $cursor;

    /**
     * @var mixed
     */
    private $node;

    public function __construct(string $cursor, $node)
    {
        $this->cursor = $cursor;
        $this->node = $node;
    }

    public function cursor(): string
    {
        return $this->cursor;
    }

    /**
     * @return mixed
     */
    public function node()
    {
        return $this->node;
    }
}
