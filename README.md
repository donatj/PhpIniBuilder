# PHP INI Builder

[![Latest Stable Version](https://poser.pugx.org/donatj/php-ini-builder/version)](https://packagist.org/packages/donatj/php-ini-builder)
[![License](https://poser.pugx.org/donatj/php-ini-builder/license)](https://packagist.org/packages/donatj/php-ini-builder)
[![ci.yml](https://github.com/donatj/PhpIniBuilder/actions/workflows/ci.yml/badge.svg)](https://github.com/donatj/PhpIniBuilder/actions/workflows/ci.yml)


Build INI strings from PHP arrays.

Keep configuration close to the code that uses it. Use the result with `parse_ini_string()`. Write it to a file for `parse_ini_file()`.

It handles sections. It handles nested keys. It handles lists.

## Requirements

- **php**: >=8.2
- **ext-ctype**: *

## Installing

Install the latest version with:

```bash
composer require 'donatj/php-ini-builder'
```

## Upgrading

See [UPGRADE.md](UPGRADE.md) for migration notes.

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

```ini
[Title]
str = awesome
int = 7
flt = 10.2


[Title 2]
bool = true

arr[] = a
arr[] = b
arr[] = c
arr[6] = d
arr[] = e
arr[key] = f
```

## Documentation

### Class: donatj\Ini\Builder

Builds an INI string from an array.

#### Method: Builder->__construct

```php
function __construct(
	bool $enableBool = true,
	bool $enableNumeric = true,
	bool $enableAlphanumeric = true,
	bool $skipNullValues = false,
)
```

##### Parameters

- ***bool*** `$enableBool` - Enable automatic boolean detection?
- ***bool*** `$enableNumeric` - Enable automatic numeric detection?
- ***bool*** `$enableAlphanumeric` - Enable automatic alphanumeric detection?
- ***bool*** `$skipNullValues` - Skip null values?

---

#### Method: Builder->generate

```php
function generate(array $data): string
```

INI String Result

##### Parameters

- ***array<int|string,mixed>*** `$data`

**Throws**: `\donatj\Ini\ExceededMaxDepthException` - When an array is nested more than two levels below the root.

**Throws**: `\TypeError` - When a leaf value is not a scalar, null, or stringable object.

---

#### Method: Builder->__invoke

```php
function __invoke(array $data): string
```

Same as `generate` - exists to make Builder callable.

##### Parameters

- ***array<int|string,mixed>*** `$data`

---

#### Method: Builder->escape

```php
function escape(bool|float|int|string|\Stringable|null $value): string
```

Escapes values according to the current rules.

---

#### Method: Builder->withBoolDetection

```php
function withBoolDetection(bool $enableBool): static
```

Enable or disable automatic boolean detection.  
  
PHP's built-in `parse_ini_*` methods parse `1`, `'1'`, and `true`. They parse `''` and `false` to the same  
values when the scanner mode is set to `INI_SCANNER_NORMAL`, enabling this option causes these values to be  
output as `true` / `false`. The original builder is not changed.

---

#### Method: Builder->withNumericDetection

```php
function withNumericDetection(bool $enableNumeric): static
```

Enable or disable automatic numeric detection.  
  
PHP's built-in `parse_ini_*` methods parse all values to strings. Enabling this option enables numeric detection  
so they will be output once again as floats/ints. The original builder is not changed.

---

#### Method: Builder->withAlphanumericDetection

```php
function withAlphanumericDetection(bool $enableAlphanumeric): static
```

Enable or disable automatic alphanumeric detection.  
  
PHP's built-in `parse_ini_*` methods do not require quotation marks around simple strings without spaces.  
Enabling this option removes the quotation marks on said simple strings. The original builder is not changed.

---

#### Method: Builder->withSkipNullValues

```php
function withSkipNullValues(bool $skipNullValues): static
```

Enable or disable skipping null values.  
  
When enabled, null values will be skipped. The original builder is not changed.

### Class: donatj\Ini\ExceededMaxDepthException

Exception thrown when the max depth supported by INI is exceeded.