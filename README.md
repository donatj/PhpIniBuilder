# PHP INI Builder

[![Latest Stable Version](https://poser.pugx.org/donatj/php-ini-builder/version)](https://packagist.org/packages/donatj/php-ini-builder)
[![License](https://poser.pugx.org/donatj/php-ini-builder/license)](https://packagist.org/packages/donatj/php-ini-builder)
[![Coverage Status](https://coveralls.io/repos/github/donatj/PhpIniBuilder/badge.svg)](https://coveralls.io/github/donatj/PhpIniBuilder)
[![ci.yml](https://github.com/donatj/PhpIniBuilder/actions/workflows/ci.yml/badge.svg)](https://github.com/donatj/PhpIniBuilder/actions/workflows/ci.yml)


PHP INI Builder converts PHP arrays into INI configuration text.

Use scalar values for INI entries. Use top-level arrays for sections. Nested arrays produce INI keys and lists. The builder quotes strings as needed and formats booleans and numbers for PHP's INI parser.

Pass the generated text to `parse_ini_string()`. Write it to a file for `parse_ini_file()`.

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

## Examples

### Basic configuration

A top-level array creates an INI section:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

$data = [
	'server' => [
		'host'    => 'localhost',
		'port'    => 8080,
		'enabled' => true,
	],
];

$builder = new \donatj\Ini\Builder;

echo $builder->generate($data);

```

It produces:

```ini
[server]
host = localhost
port = 8080
enabled = true
```

### Application configuration

Nested arrays create section keys. Sequential numeric keys create lists:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

$data = [
	'environment' => 'production',
	'application' => [
		'name'            => 'inventory-api',
		'port'            => 8080,
		'log_path'        => '/var/log/inventory-api',
		'trusted_proxies' => [
			'10.0.0.10',
			'10.0.0.11',
		],
	],
	'database'    => [
		'host'    => 'db.internal',
		'port'    => 3306,
		'options' => [
			'timeout'    => 5,
			'persistent' => false,
		],
	],
];

$builder = new \donatj\Ini\Builder;

echo $builder->generate($data);

```

It produces:

```ini
environment = production


[application]
name = 'inventory-api'
port = 8080
log_path = '/var/log/inventory-api'

trusted_proxies[] = '10.0.0.10'
trusted_proxies[] = '10.0.0.11'


[database]
host = 'db.internal'
port = 3306

options[timeout] = 5
options[persistent] = false
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

**Throws**: `\donatj\Ini\TypeError` - When a leaf value is not a scalar, null, or stringable object.

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

### Class: donatj\Ini\TypeError

Thrown when a value cannot be represented in an INI string.