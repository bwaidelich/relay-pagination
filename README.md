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
To navigate through results in _reversed_ order, the `reverse` parameter of the `Paginator` constructor can be specified:

```php
$loader = new ArrayLoader(range('a', 'e'));
$paginator = new Paginator($loader, true);

$page1 = $paginator->first(3);

Assert::same(['e', 'd', 'c'], array_map(fn($edge) => $edge->node(), $page1->toArray()));
Assert::false($page1->pageInfo()->hasPreviousPage());
Assert::true($page1->pageInfo()->hasNextPage());

$page2 = $paginator->first(3, $page1->pageInfo()->endCursor());
Assert::same(['b', 'a'], array_map(fn($edge) => $edge->node(), $page2->toArray()));
Assert::true($page2->pageInfo()->hasPreviousPage());
Assert::false($page2->pageInfo()->hasNextPage());
```

## Examples

See [examples](examples) folder
