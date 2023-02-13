<?php
declare(strict_types=1);
namespace Wwwision\RelayPagination\Loader;

use Doctrine\DBAL\Query\QueryBuilder;
use Wwwision\RelayPagination\Connection\Edges;

/**
 * Loader that allows to paginate arbitrary DBAL results
 *
 * Note: This loader requires the doctrine/dbal package to be installed
 *
 * Usage:
 *
 * $queryBuilder = (new QueryBuilder($dbalConnection))
 *   ->select('*')
 *   ->from('some_table');
 * $loader = new DbalLoader($queryBuilder, 'id');
 */
final class DbalLoader implements Loader
{
    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        private readonly string $cursorField,
    ) {}

    public function first(int $limit, string $startCursor = null): Edges
    {
        $queryBuilder = clone $this->queryBuilder;
        $queryBuilder
            ->orderBy($this->cursorField, 'ASC')
            ->setMaxResults($limit);
        if ($startCursor !== null) {
            if ($queryBuilder->getQueryPart('groupBy') === []) {
                $queryBuilder = $queryBuilder->andWhere("$this->cursorField >= :startCursor");
            } else {
                $queryBuilder = $queryBuilder->andHaving("$this->cursorField >= :startCursor");
            }
            $queryBuilder->setParameter('startCursor', $startCursor);
        }
        return Edges::fromRawArray($queryBuilder->execute()->fetchAllAssociative(), $this->cursorField);
    }

    public function last(int $limit, string $endCursor = null): Edges
    {
        $queryBuilder = clone $this->queryBuilder;
        $queryBuilder
            ->orderBy($this->cursorField, 'DESC')
            ->setMaxResults($limit);
        if ($endCursor !== null) {
            if ($queryBuilder->getQueryPart('groupBy') === []) {
                $queryBuilder = $queryBuilder->andWhere("$this->cursorField <= :endCursor");
            } else {
                $queryBuilder = $queryBuilder->andHaving("$this->cursorField <= :endCursor");
            }
            $queryBuilder->setParameter('endCursor', $endCursor);
        }
        return Edges::fromRawArray(array_reverse($queryBuilder->execute()->fetchAllAssociative()), $this->cursorField);
    }
}
