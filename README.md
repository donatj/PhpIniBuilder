# PHP INI Builder

[![Latest Stable Version](https://poser.pugx.org/donatj/php-ini-builder/version)](https://packagist.org/packages/donatj/php-ini-builder)
[![License](https://poser.pugx.org/donatj/php-ini-builder/license)](https://packagist.org/packages/donatj/php-ini-builder)
[![ci.yml](https://github.com/donatj/phpUserAgent/actions/workflows/ci.yml/badge.svg?)](https://github.com/donatj/phpUserAgent/actions/workflows/ci.yml)


Create PHP `parse_ini_string` / `parse_ini_file` compatible INI strings from associative arrays.

## Requirements

- **php**: >=7.1
- **ext-ctype**: *

## Installing

Install the latest version with:

```bash
composer require 'donatj/php-ini-builder'
```

## Example

Here is a simple example script:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

$data = [
	'Title'   => [
		'str' => 'awesome',
		'int' => 7,
		'flt' => 10.2,
	],
	'Title 2' => [
		'bool' => true,
		'arr'  => [
			'a', 'b', 'c', 6 => 'd', 'e', 'key' => 'f',
		],
	],
];

$builder = new \donatj\Ini\Builder;

echo $builder->generate($data);

```

Which outputs:

```php
[Title]
str = 'awesome'
int = 7
flt = 10.2

[Title 2]
bool = true
arr[] = 'a'
arr[] = 'b'
arr[] = 'c'
arr[6] = 'd'
arr[] = 'e'
arr[key] = 'f'

```

## Documentation

[See: DOCS.md](DOCS.md)