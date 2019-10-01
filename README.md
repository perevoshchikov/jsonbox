# Anper\Jsonbox

[![Software License][ico-license]](LICENSE.md)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-ga]][link-ga]

PHP wrapper / Client SDK for [jsonbox](https://github.com/vasanthv/jsonbox)

## Install

``` bash
$ composer require anper/jsonbox
```

## Usage
```php
use Anper\Jsonbox\Jsonbox;

$jsonbox = Jsonbox::factory('box-id');
```

**Create**
```php
// Create a record
$response = $jsonbox->create(['name' => 'Arya Stark']);

// Create multiple records
$response = $jsonbox->create([
    ['name' => 'Daenerys Targaryen'],
    ['name' => 'Arya Stark'],
]);

// Create a collection
$response = $jsonbox->colletion('users')
    ->create(/* like one record or multiple records */);   
```

**Read**
```php
// Read a record
$response = $jsonbox->record('5d776a25fd6d3d6cb1d45c51')->read();

// Read a collection
$response = $jsonbox->collection('users')->read();

// Read all
$response = $jsonbox->read();
```

**Filtering**
```php
$filter = new \Anper\Jsonbox\Filter();
$filter->equalTo('name', 'Arya Stark');

// Filter all
$response = $jsonbox->read($filter);

// Filter collection
$response = $jsonbox->collection('users')->read($filter);
```

**Update**
```php
// Update a record
$response = $jsonbox->record('5d776a25fd6d3d6cb1d45c51')
    ->update(['name' => 'Arya Stark']);

// Update multiple records (async request for every record)
$response = $jsonbox->update([
    '5d776b75fd6d3d6cb1d45c52' => ['name' => 'Daenerys Targaryen'],
    '5d776b75fd6d3d6cb1d45c53' => ['name' => 'Arya Stark'],
]);
```

**Delete**
```php
// Delete a record
$response = $jsonbox->record('5d776a25fd6d3d6cb1d45c51')->delete();

// Delete multiple records (async request for every record)
$response = $jsonbox->delete([
    '5d776b75fd6d3d6cb1d45c52',
    '5d776b75fd6d3d6cb1d45c53',
]);

// Delete by filter
$response = $jsonbox->delete($filter);
```

## Test

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/anper/jsonbox.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-ga]: https://github.com/perevoshchikov/jsonbox/workflows/Tests/badge.svg

[link-packagist]: https://packagist.org/packages/anper/jsonbox
[link-ga]: https://github.com/perevoshchikov/jsonbox/actions