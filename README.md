# Reviso REST Api - PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/webleit/revisoapi.svg?style=flat-square)](https://packagist.org/packages/webleit/revisoapi)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/Weble/RevisoApi/run-tests?label=tests)](https://github.com/webleit/revisoapi/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/Weble/RevisoApi/php-cs-fixer?label=code%20style)](https://github.com/weble/revisoapi/actions?query=workflow%3A"php-cs-fixer"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/webleit/revisoapi.svg?style=flat-square)](https://packagist.org/packages/webleit/revisoapi)
<!--delete-->
---

This Library is a SDK in PHP that simplifies the usage of the Reviso REST API (http://api-docs.reviso.com)

It provides both an interface to ease the interaction with the APIs without bothering with the actual REST request, while packaging the various responses using very simple Model classes that can be then uses with any other library or framework.

## Installation 

```
composer require webleit/revisoapi
```

### HTTP Clients

In order to talk to Reviso APIs, you need an HTTP Client library. Since v2 of this package, the HTTP Client is not included, allowing you to choose the one you like the best, and avoiding any potential dependency conflict.

You can use any library to send HTTP messages  that implements [php-http/client-implementation](https://packagist.org/providers/php-http/client-implementation).

Here is a list of all officially supported clients and adapters: <http://docs.php-http.org/en/latest/clients.html>

You can read more on the [HTTPlug docs](http://docs.php-http.org/en/latest/httplug/users.html).

## Usage

In order to use the library, just require the composer autoload file, and then fire up the library itself.

```php
require './vendor/autoload.php';
$reviso = new \Weble\RevisoApi\Reviso($appSecretToken, $agreementGrantToken);
```

This way, the library will try to find any HTTP Client implementation that you may already have.
If you want, you can pass a specific Http Client instance to the library like this:

```php
require './vendor/autoload.php';
$reviso = new \Weble\RevisoApi\Reviso($appSecretToken, $agreementGrantToken, $yourHttpClient);
```

If you want to use the demo account, just don't specify the auth parameters, and you'll be able to use any
GET request.

```php
$reviso = new \Weble\RevisoApi\Reviso();
```

## API calls

To call any Api, just use the same name reported in the api docs.
You can get the list of supported apis using the getEndpoints() method

```php 
$reviso->getEndpoints();
```

You can, for example, get the list of customers by using:

```php
$customers = $reviso->customers->get();
```

or the list of customer groups

```php
$groups = $reviso->customerGroups->get();
```

### List calls

To get a list of resources from a module, use the get() method

```php
$customers = $reviso->customers->get();
```

In order to navigate the pages, just use the "page" and "perPage" methods

```php
$customers = $reviso->customers->page(1)->perPage(100)->get();
```



### Filters

To filter a list of resources from a module, use the `where()` method before calling `get()`

```php
$customers = $reviso->customers->where('corporateIdentificationNumber', '=', '123456789')->get();
```

In order to navigate the pages, just use the "page" and "perPage" methods

```php
$customers = $reviso->customers->page(1)->perPage(100)->get();
```

## Return Types

Any "list" api call returns a Collection object, which contains information on the list itself, allows for further pagination, 
and stores the list of items in a Laravel Collection package (`Illuminate\Support\Collection`).
You can therefore use the result as Collection, which allows mapping, reducing, serializing, etc

```php
$customers = $reviso->customers->get();

$data = $customers->toArray();
$json = $customers->toJson();

// After fetch filtering in php
$filtered = $customers->where('accountNumber', '>', 200);

// Grouping
$filtered = $customers->groupBy('country');

```

Any "resource" api call returns a Model object of a class dedicated to the single resource you're fetching.
For example, calling

```php
$customer = $reviso->customers->get($accuntNumber);
$data = $customer->toArray();
$name = $customer->name;

```

will return a `\Weble\RevisoApi\Model` object, which is Arrayable and Jsonable, and that can be therefore used in many ways.

## CRUD

You can create / Read / Update / Delete a resource from the Endpoint class or on the model itself.

### Create
```php
$data = [
    /** Data of the customer */
];
$customer = $reviso->customers->create($data);
```

### Read
```php
$customer = $reviso->customers->find($accountNumber);
```

### Update
```php
$data = [
    /** Data of the customer */
];
$customer = $reviso->customers->find($accountNumber);
$customer->save($data);
```

### Delete
```php
$data = [
    /** Data of the customer */
];
$customer = $reviso->customers->find($accountNumber);
$customer->delete();
```

## Test
This package contains some tests to test the basic functionalities of the package.
In order to run the tests also on the "CRUD" methods, you need to create a `config.json` file in the`"tests/` directory, with the authentication details of an app you want to use as a test base.
You may copy if from the `config.example.json` in the same directory.

```bash
composer test
```

## Upgrade from V1 to V2

[See the upgrade docs](UPGRADE.md)

## Contributing

Finding bugs, sending pull requests or improving the docs - any contribution is welcome and highly appreciated

## Versioning

Semantic Versioning Specification (SemVer) is used.

## Copyright and License

Copyright Weble Srl under the MIT license.
