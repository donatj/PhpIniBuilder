# PHP INI Builder

[![Latest Stable Version](https://poser.pugx.org/donatj/php-ini-builder/v/stable.png)](https://packagist.org/packages/donatj/php-ini-builder)
[![License](https://poser.pugx.org/donatj/php-ini-builder/license.png)](https://packagist.org/packages/donatj/php-ini-builder)
[![Build Status](https://travis-ci.org/donatj/PhpIniBuilder.svg?branch=master)](https://travis-ci.org/donatj/PhpIniBuilder)

Create PHP `parse_ini_string` / `parse_ini_file` compatible INI strings from associative arrays.

## Requirements

- PHP 5.3+

## Installing

PHP INI Builder is available through Packagist via Composer

```json
{
    "require": {
        "donatj/php-ini-builder": "dev-master"
    }
}
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

$x = new \donatj\Ini\Builder($data);
echo $x->generate();
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

### Class: Builder \[ `\donatj\Ini` \]

Utility for Converting An Array to a INI string

#### Method: `Builder->__construct([ $data = null [, $enableBool = true [, $enableNumeric = true]]])`

##### Parameters:

- ***array*** `$data`
- ***bool*** `$enableBool` - Enable bool detection
- ***bool*** `$enableNumeric` - Enable numeric detection



---

#### Method: `Builder->setData($data)`

##### Parameters:

- ***array*** | ***null*** `$data`



---

#### Method: `Builder->getData()`

##### Returns:

- ***array*** | ***null***


---

#### Method: `Builder->generate()`

INI String Result  
  


##### Returns:

- ***string***


---

#### Method: `Builder->enableBoolDetection($enableBool)`

Enable / Disable Automatic Boolean Detection  
PHP's built in `parse_ini_*` methods parse `1`, `'1'` and `true` and likewise `''`, and `false` to the same values  
when the scanner mode is set to `INI_SCANNER_NORMAL`, enabling this option causes these values to be output  
as `true` / `false`  


##### Parameters:

- ***bool*** `$enableBool`



---

#### Method: `Builder->enableNumericDetection($enableNumeric)`

Enable / Disable Automatic Numeric Detection  
PHP's built in `parse_ini_*` methods parse all values to string, enabling this option enables numeric detection  
so they will be output once again as floats/ints  


##### Parameters:

- ***boolean*** `$enableNumeric`

