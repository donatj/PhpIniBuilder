# PHP INI Builder

[![Latest Stable Version](https://poser.pugx.org/donatj/php-ini-builder/version)](https://packagist.org/packages/donatj/php-ini-builder)
[![License](https://poser.pugx.org/donatj/php-ini-builder/license)](https://packagist.org/packages/donatj/php-ini-builder)
[![Build Status](https://travis-ci.org/donatj/PhpIniBuilder.svg?branch=master)](https://travis-ci.org/donatj/PhpIniBuilder)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/donatj/PhpIniBuilder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/donatj/PhpIniBuilder)


Create PHP `parse_ini_string` / `parse_ini_file` compatible INI strings from associative arrays.

## Requirements

- **php**: >=5.3.0
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

require(__DIR__ . '/../vendor/autoload.php');

$data = array(
	'Title' => array(
		'str' => 'awesome',
		'int' => 7,
		'flt' => 10.2,
	),
	'Title 2' => array(
		'bool' => true,
		'arr' => array(
			'a', 'b', 'c', 6 => 'd', 'e', 'key' => 'f'
		)
	)
);

$x = new \donatj\Ini\Builder();
echo $x->generate($data);
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