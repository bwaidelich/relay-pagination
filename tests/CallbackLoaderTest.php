<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests;

use Wwwision\RelayPagination\Loader\ArrayLoader;
use Wwwision\RelayPagination\Loader\CallbackLoader;

final class CallbackLoaderTest extends LoaderTestBase
{
    public function setUp(): void
    {
        $wrappedLoader = new ArrayLoader(range('a', 'i'));
        $this->loader = new CallbackLoader(
            fn(int $limit, string $startCursor = null) => $wrappedLoader->first($limit, $startCursor),
            fn(int $limit, string $endCursor = null) => $wrappedLoader->last($limit, $endCursor)
        );
    }

    protected function renderNode($node): string
    {
        return (string)$node;
    }
}
