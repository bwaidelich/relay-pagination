<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Wwwision\RelayPagination\Connection\Edge;
use Wwwision\RelayPagination\Loader\DbalLoader;

final class DbalPaginatorTest extends AbstractPaginatorTest
{
    public function setUp(): void
    {
        $connection = DriverManager::getConnection(['url' => 'sqlite:///:memory:']);
        $connection->executeQuery('CREATE TABLE fixture (id INTEGER, value TEXT)');
        foreach (range('a', 'k') as $id => $value) {
            $connection->insert('fixture', compact('id', 'value'));
        }
        $queryBuilder = new QueryBuilder($connection);
        $queryBuilder
            ->select('*')
            ->from('fixture');
        $this->loader = new DbalLoader($queryBuilder, 'id');
    }

    protected function renderEdge(Edge $edge): string
    {
        return $edge->node()['value'];
    }

}
