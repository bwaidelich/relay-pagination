<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests;

use Wwwision\RelayPagination\Loader\ArrayLoader;

final class ArrayLoaderTest extends LoaderTestBase
{
    public function setUp(): void
    {
        $this->loader = new ArrayLoader(range('a', 'i'));
    }

    protected function renderNode($node): string
    {
        return (string)$node;
    }

    public function test_first_throws_exception_for_invalid_cursor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->loader->first(123, 'invalid');
    }

    public function test_last_throws_exception_for_invalid_cursor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->loader->last(123, 'invalid');
    }
}
