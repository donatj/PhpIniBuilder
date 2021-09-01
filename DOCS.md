# Class: \donatj\Ini\Builder

Utility for Converting An Array to a INI string

## Method: Builder->__construct

```php
function __construct([ bool $enableBool = true [, bool $enableNumeric = true [, bool $enableAlphaNumeric = true [, bool $skipNullValues = false]]]])
```

### Parameters:

- ***bool*** `$enableBool` - Enable automatic boolean detection?
- ***bool*** `$enableNumeric` - Enable automatic numeric detection?
- ***bool*** `$enableAlphaNumeric` - Enable automatic alpha-numeric detection?
- ***bool*** `$skipNullValues` - Skip null values?

---

## Method: Builder->generate

```php
function generate(array $data) : string
```

INI String Result

---

## Method: Builder->__invoke

```php
function __invoke(array $data) : string
```

Same as `generate` - exists to make Builder callable.

---

## Method: Builder->escape

```php
function escape($value) : string
```

Escapes Values According to Currently Set Rules

### Parameters:

- ***bool*** | ***float*** | ***int*** | ***string*** | ***null*** `$value`

---

## Method: Builder->enableBoolDetection

```php
function enableBoolDetection(bool $enableBool) : void
```

Enable / Disable Automatic Boolean Detection  
  
PHP's built in `parse_ini_*` methods parse `1`, `'1'` and `true` and likewise `''`, and `false` to the same  
values when the scanner mode is set to `INI_SCANNER_NORMAL`, enabling this option causes these values to be  
output as `true` / `false`

---

## Method: Builder->enableNumericDetection

```php
function enableNumericDetection(bool $enableNumeric) : void
```

Enable / Disable Automatic Numeric Detection  
  
PHP's built in `parse_ini_*` methods parse all values to string. Enabling this option enables numeric detection  
so they will be output once again as floats/ints

---

## Method: Builder->enableAlphaNumericDetection

```php
function enableAlphaNumericDetection(bool $enableAlphaNumeric) : void
```

Enable / Disable Automatic AlphaNumeric Detection  
  
PHP's built in `parse_ini_*` methods does not require quotation marks around simple strings without spaces.  
Enabling this option removes the quotation marks on said simple strings.

---

## Method: Builder->enableSkipNullValues

```php
function enableSkipNullValues(bool $skipNullValues) : void
```

Enable / Disable Skipping Null Values  
  
When enabled, null values will be skipped.

# Class: \donatj\Ini\ExceededMaxDepthException

Exception thrown when the max depth supported by INI is exceeded.