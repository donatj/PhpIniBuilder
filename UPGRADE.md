# Upgrading to v0.4.0

v0.4.0 is a breaking release. It requires PHP 8.2 or later.

## Builder configuration

`Builder` configuration is immutable. The `enable*` methods were removed. Chain the matching `with*` methods from a new builder:

```php
$builder = (new Builder)
	->withBoolDetection(false)
	->withNumericDetection(false)
	->withAlphanumericDetection(false)
	->withSkipNullValues(true);
```

`enableAlphaNumericDetection()` became `withAlphanumericDetection()`. The constructor named argument changed from `$enableAlphaNumeric` to `$enableAlphanumeric`.

## Extending Builder

`Builder` no longer supports subclass customization. Its internal state and `build()` method are private.

## Input validation

`generate()` accepts scalar values, `null`, and stringable objects as INI values. Unsupported values throw `donatj\Ini\TypeError`.
