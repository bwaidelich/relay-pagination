# Relay Cursor Pagination

Simple pagination implementing the Cursor Connections Specification, see https://relay.dev/graphql/connections.htm

## Installation

Install via [composer](https://getcomposer.org/):

    composer require wwwision/relay-pagination

## Usage

```php
$loader = # ... instance of \Wwwision\RelayPagination\Loader\Loader
$resultsPerPage = 5; // edges to load per page

$paginator = new Paginator($loader);
$firstPage = $paginator->first($resultsPerPage);

foreach ($firstPage as $edge) {
    // $edge->cursor(); contains the cursor string
    // $edge->node(); contains the payload
}

// $firstPage->pageInfo(); contains an object with pagination information
```

### Next page(s)

Every `connection` contains information about next and previous pages.
To navigate to a succeeding page, you can use the `endCursor` of the previous connection as input for the `after` argument:

```php
if ($firstPage->pageInfo()->hasNextPage()) {
  $secondPage = $paginator->first($resultsPerPage, $firstPage->pageInfo()->endCursor());
  // ...
}
```

### Backwards navigation

To navigate to a _preceeding_ page, the `startCursor` can be passed to the `last()` method likewise:

```php
if ($secondPage->pageInfo()->hasPreviousPage()) {
  $firstPage = $paginator->last($resultsPerPage, $secondPage->pageInfo()->startCursor());
  // ...
}
```

### Reversed order

The ordering of edges is the same whether forward or backward navigation is used (as defined in the [specification](https://relay.dev/graphql/connections.htm#sec-Edge-order)).
To navigate through results in _reversed_ order, the `reversed()` method of the `Paginator` can be used:

```php
$loader = new ArrayLoader(range('a', 'e'));
$paginator = (new Paginator($loader))->reversed();

$page1 = $paginator->first(3);

Assert::same(['e', 'd', 'c'], array_map(fn($edge) => $edge->node(), $page1->toArray()));
Assert::false($page1->pageInfo()->hasPreviousPage());
Assert::true($page1->pageInfo()->hasNextPage());

$page2 = $paginator->first(3, $page1->pageInfo()->endCursor());
Assert::same(['b', 'a'], array_map(fn($edge) => $edge->node(), $page2->toArray()));
Assert::true($page2->pageInfo()->hasPreviousPage());
Assert::false($page2->pageInfo()->hasNextPage());
```

## Loaders

This package comes with three adapters (aka "loaders"):

### ArrayLoader

The `ArrayLoader` allows to paginate arbitrary arrays.
**Note:** This loader is mainly meant for testing and debugging purposes and it should not be used for large datasets because the whole array has to be loaded into memory, obviously.

#### Usage

```php
$arrayLoader = new ArrayLoader($arbitraryArray);
```

**Note:** The specified array can be associative, but the keys will be lost during pagination since the ArrayLoader only works with the array values in order to guarantee deterministic ordering.

### CallbackLoader

The `CallbackLoader` invokes closures to determine pagination results.
**Note:**  This loader is mainly meant for rapid development, testing & debugging purposes. Usually you'd want to create a custom implementation of the Loader interface instead

#### Usage

```php
$callbackLoader = new CallbackLoader(
    fn(int $limit, string $startCursor = null) => Edges::fromRawArray(...),
    fn(int $limit, string $endCursor = null) => Edges::fromRawArray(...)
);
```

### DbalLoader

The `DbalLoader` allows to paginate arbitrary DBAL results.
**Note:** This loader requires the `doctrine/dbal` package to be installed:

    composer require doctrine/dbal

#### Usage

```php
$queryBuilder = (new QueryBuilder($dbalConnection))
  ->select('*')
  ->from('some_table');
$dbalLoader = new DbalLoader($queryBuilder, 'id');
```

## Convert nodes

A `node` is the only untyped property in this package since the loaders define the structure & type of nodes.
For the `DbalLoader` a node is an associative array containing the raw result from the corresponding database row for example.

In order to make it easier to re-use loaders a `Node Converter` can be specified that is applied to all results before it is returned:

```php
$paginator = (new Paginator($someLoader))
  ->withNodeConverter(fn(string $node) => json_decode($node));
```

The above example expects the nodes to contain a valid JSON string.
The same mechanism can be used in order to convert database results to a dedicated domain model instance:

```php
$paginator = (new Paginator($dbalLoader))
  ->withNodeConverter(fn(array $row) => MyModel::fromDatabaseRow($row));
```

## Examples

See [examples](examples) folder
