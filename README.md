# PHP INI Builder

Create `parse_ini_string` / `parse_ini_file` compatible INI strings from associative arrays.

## Example

The following is a simple example usage.

```php
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

Outputs:

```ini
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

### Class: Builder - `\donatj\Ini\Builder`

Utility for Converting An Array to a INI string

#### Method: `Builder`->`__construct($data [, $enableBool = true [, $enableNumeric = true]])`

##### Parameters

- ***array*** `$data`
- ***bool*** `$enableBool` - Enable bool detection
- ***bool*** `$enableNumeric` - Enable numeric detection



---

#### Method: `Builder`->`generate()`

INI String Result  
  


##### Returns

- ***string***


---

#### Method: `Builder`->`enableBoolDetection($enableBool)`

Enable / Disable Automatic Boolean Detection  
PHP's built in `parse_ini_*` methods parse `1`, `'1'` and `true` and likewise `''`, and `false` to the same values  
when the scanner mode is set to `INI_SCANNER_NORMAL`, enabling this option causes these values to be output  
as `true` / `false`  


##### Parameters

- ***bool*** `$enableBool`



---

#### Method: `Builder`->`enableNumericDetection($enableNumeric)`

Enable / Disable Automatic Numeric Detection  
PHP's built in `parse_ini_*` methods parse all values to string, enabling this option enables numeric detection  
so they will be output once again as floats/ints  


##### Parameters

- ***boolean*** `$enableNumeric`



### Class: ExceededMaxDepthException - `\donatj\Ini\ExceededMaxDepthException`

...
