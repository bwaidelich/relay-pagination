<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Tests;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Wwwision\RelayPagination\Loader\OrmLoader;
use Wwwision\RelayPagination\Tests\Fixture\Entity;

final class OrmPaginatorTest extends PaginatorTestBase
{
    public function setUp(): void
    {
        $config = ORMSetup::createAttributeMetadataConfiguration([__DIR__ . '/Fixture'], isDevMode: true);
        $connection = DriverManager::getConnection(['url' => 'sqlite:///:memory:'], $config);
        $entityManager = new EntityManager($connection, $config);

        $connection->executeQuery('CREATE TABLE entity (id INTEGER, value TEXT)');

        foreach (range('a', 'k') as $id => $value) {
            $entityManager->persist(new Entity($id, $value));
        }
        $entityManager->flush();

        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('e')
            ->from(Entity::class, 'e')
            ->orderBy('e.id', 'ASC');

        $this->loader = new OrmLoader($queryBuilder, 'id');
    }

    protected function renderNode($node): string
    {
        return $node->value;
    }

}
