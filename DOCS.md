## Class: \donatj\Ini\Builder

Utility for Converting An Array to a INI string

### Method: Builder->__construct

```php
function __construct([ $enableBool = true [, $enableNumeric = true [, $enableAlphaNumeric = true [, $skipNullValues = false]]]])
```

#### Parameters:

- ***bool*** `$enableBool`
- ***bool*** `$enableNumeric`
- ***bool*** `$enableAlphaNumeric`
- ***bool*** `$skipNullValues`

---

### Method: Builder->generate

```php
function generate(array $data)
```

INI String Result

#### Parameters:

- ***array*** `$data`

#### Returns:

- ***string***

---

### Method: Builder->__invoke

```php
function __invoke(array $data)
```

#### Parameters:

- ***array*** `$data`

#### Returns:

- ***string***

---

### Method: Builder->escape

```php
function escape($value)
```

Escapes Values According to Currently Set Rules

#### Parameters:

- ***mixed*** `$value`

#### Returns:

- ***string***

---

### Method: Builder->enableBoolDetection

```php
function enableBoolDetection($enableBool)
```

Enable / Disable Automatic Boolean Detection  
  
PHP's built in `parse_ini_*` methods parse `1`, `'1'` and `true` and likewise `''`, and `false` to the same  
values when the scanner mode is set to `INI_SCANNER_NORMAL`, enabling this option causes these values to be  
output as `true` / `false`

#### Parameters:

- ***bool*** `$enableBool`

---

### Method: Builder->enableNumericDetection

```php
function enableNumericDetection($enableNumeric)
```

Enable / Disable Automatic Numeric Detection  
  
PHP's built in `parse_ini_*` methods parse all values to string. Enabling this option enables numeric detection  
so they will be output once again as floats/ints

#### Parameters:

- ***bool*** `$enableNumeric`

---

### Method: Builder->enableAlphaNumericDetection

```php
function enableAlphaNumericDetection($enableAlphaNumeric)
```

Enable / Disable Automatic AlphaNumeric Detection  
  
PHP's built in `parse_ini_*` methods does not require quotation marks around simple strings without spaces.  
Enabling this option removes the quotation marks on said simple strings.

#### Parameters:

- ***bool*** `$enableAlphaNumeric`

---

### Method: Builder->enableSkipNullValues

```php
function enableSkipNullValues($skipNullValues)
```

Enable / Disable Skipping Null Values  
  
When enabled, null values will be skipped.

#### Parameters:

- ***bool*** `$skipNullValues`

## Class: \donatj\Ini\ExceededMaxDepthException

Exception thrown when the max depth supported by INI is exceeded.