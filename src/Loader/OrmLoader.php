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
    ) {}

    public function first(int $limit, string $startCursor = null): Edges
    {
        $queryBuilder = clone $this->queryBuilder;
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->orderBy("$rootAlias.$this->cursorPropertyName", 'ASC')
            ->setMaxResults($limit);
        if ($startCursor !== null) {
            if ($queryBuilder->getDQLPart('groupBy') === []) {
                $queryBuilder = $queryBuilder->andWhere("$rootAlias.$this->cursorPropertyName >= :startCursor");
            } else {
                $queryBuilder = $queryBuilder->andHaving("$rootAlias.$this->cursorPropertyName >= :startCursor");
            }
            $queryBuilder->setParameter('startCursor', $startCursor);
        }
        return Edges::fromArray(array_map(fn ($node) => new Edge($this->getCursorValue($node), $node), $queryBuilder->getQuery()->execute()));
    }

    public function last(int $limit, string $endCursor = null): Edges
    {
        $queryBuilder = clone $this->queryBuilder;
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->orderBy("$rootAlias.$this->cursorPropertyName", 'DESC')
            ->setMaxResults($limit);
        if ($endCursor !== null) {
            if ($queryBuilder->getDQLPart('groupBy') === []) {
                $queryBuilder = $queryBuilder->andWhere("$rootAlias.$this->cursorPropertyName <= :endCursor");
            } else {
                $queryBuilder = $queryBuilder->andHaving("$rootAlias.$this->cursorPropertyName <= :endCursor");
            }
            $queryBuilder->setParameter('endCursor', $endCursor);
        }
        return Edges::fromArray(array_reverse(array_map(fn ($node) => new Edge($this->getCursorValue($node), $node), $queryBuilder->getQuery()->execute())));
    }

    private function getCursorValue($subject): string|null
    {
        if (is_array($subject)) {
            return $subject[$this->cursorPropertyName] ?? null;
        }
        if (!is_object($subject)) {
            return null;
        }
        if (array_key_exists($this->cursorPropertyName, get_object_vars($subject))) {
            return (string)$subject->{$this->cursorPropertyName};
        }
        $getterMethodName = 'get' . ucfirst($this->cursorPropertyName);
        if (is_callable([$subject, $getterMethodName])) {
            return (string)$subject->{$getterMethodName}();
        }
        if (($subject instanceof \ArrayAccess) && !($subject instanceof \SplObjectStorage) && $subject->offsetExists($this->cursorPropertyName)) {
            return $subject->offsetGet($this->cursorPropertyName);
        }
    }
}
