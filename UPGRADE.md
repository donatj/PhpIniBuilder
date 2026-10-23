# Upgrading

This major release requires PHP 8.2 or later.

## Builder configuration

`Builder` configuration is immutable. The `enable*` methods were removed. Use the matching `with*` method. Assign its returned builder.

```php
$builder = new Builder;

$builder = $builder->withBoolDetection(false);
$builder = $builder->withNumericDetection(false);
$builder = $builder->withAlphanumericDetection(false);
$builder = $builder->withSkipNullValues(true);
```

`withAlphaNumericDetection()` was renamed to `withAlphanumericDetection()`. The matching constructor named argument is now `$enableAlphanumeric`.

## Extending Builder

`Builder` no longer supports subclass customization. Its internal state and `build()` method are private.

## Input validation

`generate()` accepts scalar values, `null`, and stringable objects as INI values. Unsupported values throw `donatj\Ini\TypeError`.
