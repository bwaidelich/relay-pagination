<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Loader;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Wwwision\RelayPagination\Connection\Edge;
use Wwwision\RelayPagination\Connection\Edges;

/**
 * Loader that allows to paginate arbitrary Doctrine ORM results
 *
 * Note: This loader requires the doctrine/orm package to be installed
 *
 * Usage:
 *
 * $queryBuilder = $entityManager->createQueryBuilder()
 *   ->select('*')
 *   ->from(SomeEntity::class);
 * $loader = new OrmLoader($queryBuilder, 'id');
 */
final class OrmLoader implements Loader
{
    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        private readonly string $cursorPropertyName,
        private readonly \Closure $cursorGetter
    ) {}

    public function first(int $limit, string $startCursor = null): Edges
    {
        $queryBuilder = clone $this->queryBuilder;
        $queryBuilder
            ->orderBy($this->cursorPropertyName, 'ASC')
            ->setMaxResults($limit);
        if ($startCursor !== null) {
            $queryBuilder = $queryBuilder->andHaving("$this->cursorPropertyName >= :startCursor")->setParameter('startCursor', $startCursor);
        }
        return Edges::fromArray(array_map(fn ($node) => new Edge(($this->cursorGetter)($node), $node), $queryBuilder->getQuery()->execute()));
    }

    public function last(int $limit, string $endCursor = null): Edges
    {
        $queryBuilder = clone $this->queryBuilder;
        $queryBuilder
            ->orderBy($this->cursorPropertyName, 'DESC')
            ->setMaxResults($limit);
        if ($endCursor !== null) {
            $queryBuilder = $queryBuilder->andHaving("$this->cursorPropertyName <= :endCursor")->setParameter('endCursor', $endCursor);
        }
        return Edges::fromArray(array_reverse(array_map(fn ($node) => new Edge(($this->cursorGetter)($node), $node), $queryBuilder->getQuery()->execute())));
    }
}
