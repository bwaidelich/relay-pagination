# Relay Cursor Pagination

Simple pagination implementing the Cursor Connections Specification, see https://relay.dev/graphql/connections.htm

## Installation

Install via [composer](https://getcomposer.org/):

    composer require wwwision/relay-pagination

## Usage

```php
$loader = # ... instance of \Wwwision\RelayPagination\Loader\Loader
$numberOfNodes = 5; // nodes to load per page
$after = 'xyz'; // cursor (optional)

$paginator = new \Wwwision\RelayPagination\Paginator($loader);
$connection = $paginator->first($numberOfNodes, $after);

foreach ($connection as $edge) {
    // $edge->cursor(); contains the cursor string
    // $edge->node(); contains the payload
}

// $connection->pageInfo(); contains an object with pagination information
```

## Examples

See [examples](examples) folder
