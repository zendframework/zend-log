# PSR-3 Logger Interface compatibility

[PSR-3 Logger Interface][] is a standard recommendation defining common
interface for logging libraries. `zend-log` component predates it and have
minor incompatibilities with common interface but provides compatibility
features:

- PSR logger adapter
- PSR logger writer
- PSR placeholder processor

[PSR-3 Logger Interface]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md

## PSR logger adapter

PSR logger adapter wraps `Zend\Log\LoggerInterface` allowing it to be used
anywhere `Psr\Log\LoggerInterface` is expected.

PSR-3 log levels and `zend-log` priorities are eight [RFC 5424][] severity
levels and mapped directly.

[RFC 5424]: https://tools.ietf.org/html/rfc5424#section-6.2.1

```php
$zendLogLogger = new Zend\Log\Logger;

$psrLogger = new Zend\Log\PsrLoggerAdapter($zendLogLogger);
$psrLogger->log(Psr\Log\LogLevel::INFO, 'We have PSR compatible logger');
```

## PSR logger writer

PSR logger writer allows log messages and extras to be forwared to any PSR-3
compatible logger.

Writer needs psr logger to be useful and fallbacks to `Psr\Log\NullLogger` if
none was provided. This writer can use filters as any other writter, you can read
more in Filters section.

```php
// by passing logger as constructor parameter
$writer = new Zend\Log\Writer\Psr($psrLogger);

// by passing logger in options
$writer = new Zend\Log\Writer\Psr(['logger' => $psrLogger]);

// via setter injection
$writer = new Zend\Log\Writer\Psr;
$writer->setLogger($psrLogger);
```

## PSR placeholder processor

PsrPlaceholder processor adds support for [PSR-3 message placeholders][].
Placeholder names correspond to keys in extras array.

Value can be of arbitrary type where scalars or object implementing `__toString`
will be used directly and others will result in type printed.

[PSR-3 message placeholders]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#12-message

```php
$zendLogLogger = new Zend\Log\Logger;
$zendLogLogger->addProcessor(new Zend\Log\Processor\PsrPlaceholder);

$zendLogLogger->info('User with email {email} registered', ['email' => 'user@example.org']);
// logs message 'User with email user@example.org registered'
```
