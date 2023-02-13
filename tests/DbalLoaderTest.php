<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Wwwision\RelayPagination\Loader\DbalLoader;

final class DbalLoaderTest extends LoaderTestBase
{
    public function setUp(): void
    {
        $connection = DriverManager::getConnection(['url' => 'sqlite:///:memory:']);
        $connection->executeQuery('CREATE TABLE fixture (id INTEGER, value TEXT)');
        foreach (range('a', 'i') as $id => $value) {
            $connection->insert('fixture', compact('id', 'value'));
        }
        $queryBuilder = new QueryBuilder($connection);
        $queryBuilder
            ->select('*')
            ->from('fixture');
        $this->loader = new DbalLoader($queryBuilder, 'id');
    }

    protected function renderNode($node): string
    {
        return $node['value'];
    }

}
