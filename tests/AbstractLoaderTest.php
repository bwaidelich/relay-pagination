<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests;

use PHPUnit\Framework\TestCase;
use Wwwision\RelayPagination\Connection\Edge;
use Wwwision\RelayPagination\Loader\Loader;

abstract class AbstractLoaderTest extends TestCase
{
    protected ?Loader $loader;

    abstract protected function renderEdge(Edge $edge): string;

    public function data_provider_first(): array
    {
        return [
            ['limit' => 3, 'start_cursor' => null, 'expected' => 'abc'],
            ['limit' => 3, 'start_cursor' => '3', 'expected' => 'def'],
            ['limit' => 100, 'start_cursor' => '3', 'expected' => 'defghi'],
            ['limit' => 0, 'start_cursor' => null, 'expected' => ''],
            ['limit' => 5, 'start_cursor' => '999', 'expected' => ''],
            ['limit' => 100, 'start_cursor' => null, 'expected' => 'abcdefghi'],
            ['limit' => 100, 'start_cursor' => '0', 'expected' => 'abcdefghi'],
        ];
    }

    /**
     * @dataProvider data_provider_first
     */
    public function test_first(int $limit, ?string $startCursor, string $expected): void
    {
        $actual = implode('', $this->loader->first($limit, $startCursor)->map(fn(Edge $edge) => $this->renderEdge($edge)));
        self::assertSame($expected, $actual);
    }

    public function data_provider_last(): array
    {
        return [
            ['limit' => 3, 'end_cursor' => null, 'expected' => 'ghi'],
            ['limit' => 3, 'end_cursor' => '4', 'expected' => 'cde'],
            ['limit' => 100, 'end_cursor' => '3', 'expected' => 'abcd'],
            ['limit' => 0, 'end_cursor' => null, 'expected' => ''],
            ['limit' => 5, 'end_cursor' => '999', 'expected' => 'efghi'],
            ['limit' => 100, 'end_cursor' => null, 'expected' => 'abcdefghi'],
            ['limit' => 100, 'end_cursor' => '999', 'expected' => 'abcdefghi'],
            ['limit' => 5, 'end_cursor' => '2', 'expected' => 'abc'],
        ];
    }

    /**
     * @dataProvider data_provider_last
     */
    public function test_last(int $limit, ?string $startCursor, string $expected): void
    {
        $actual = implode('', $this->loader->last($limit, $startCursor)->map(fn(Edge $edge) => $this->renderEdge($edge)));
        self::assertSame($expected, $actual);
    }
}
