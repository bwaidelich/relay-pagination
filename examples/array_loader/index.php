<?php
use Wwwision\RelayPagination\Loader\ArrayLoader;
use Wwwision\RelayPagination\Paginator;

require __DIR__ . '../../../vendor/autoload.php';

$loader = new ArrayLoader(range('a', 'z'));
$paginator = new Paginator($loader);
$nodesPerPage = 5;

if (isset($_GET['before'])) {
    $connection = $paginator->last($nodesPerPage, $_GET['before']);
} else {
    $connection = $paginator->first($nodesPerPage, $_GET['after'] ?? null);
}

echo '<ul>';
if ($connection->pageInfo()->hasPreviousPage()) {
    echo '<li><a href="./?before=' . $connection->pageInfo()->startCursor() . '">&lt;</a></li>';
}
foreach ($connection as $edge) {
    echo "<li>{$edge->node()}</li>";
}
if ($connection->pageInfo()->hasNextPage()) {
    echo '<li><a href="./?after=' . $connection->pageInfo()->endCursor() . '">&gt;</a></li>';
}
echo '</ul>';
