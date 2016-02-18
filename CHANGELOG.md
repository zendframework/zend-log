# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.7.1 - 2016-02-18

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#28](https://github.com/zendframework/zend-log/pull/28) restores the "share
  by default" flag settings of all plugin managers back to boolean `false`,
  allowing multiple instances of each plugin type. (This restores backwards
  compatibility with versions prior to 2.7.)

## 2.7.0 - 2016-02-09

### Added

- [#7](https://github.com/zendframework/zend-log/pull/7) and
  [#15](https://github.com/zendframework/zend-log/pull/15) add a new argument
  and option to `Zend\Log\Writer\Stream` to allow setting the permission mode
  for the stream. You can pass it as the optional fourth argument to the
  constructor, or as the `chmod` option if using an options array.
- [#10](https://github.com/zendframework/zend-log/pull/10) adds `array` to the
  expected return types from `Zend\Log\Formatter\FormatterInterface::format()`,
  codifying what we're already allowing.
- [#24](https://github.com/zendframework/zend-log/pull/24) prepares the
  documentation for publication, adds a chapter on processors, and publishes it
  to https://zendframework.github.io/zend-log/

### Deprecated

- [#14](https://github.com/zendframework/zend-log/pull/14) deprecates the
  following, suggesting the associated replacements:
  - `Zend\Log\Writer\FilterPluginManager` is deprecated; use
    `Zend\Log\FilterPluginManager` instead.
  - `Zend\Log\Writer\FormatterPluginManager` is deprecated; use
    `Zend\Log\FormatterPluginManager` instead.

### Removed

- Nothing.

### Fixed

- [#14](https://github.com/zendframework/zend-log/pull/14) and
  [#17](https://github.com/zendframework/zend-log/pull/17) update the component
  to be forwards-compatible with zend-stdlib and zend-servicemanager v3.

## 2.6.0 - 2015-07-20

### Added

- [#6](https://github.com/zendframework/zend-log/pull/6) adds
  [PSR-3](http://www.php-fig.org/psr/psr-3/) support to zend-log:
  - `Zend\Log\PsrLoggerAdapter` allows you to decorate a
    `Zend\Log\LoggerInterface` instance so it can be used wherever a PSR-3
    logger is expected.
  - `Zend\Log\Writer\Psr` allows you to decorate a PSR-3 logger instance for use
    as a log writer with `Zend\Log\Logger`.
  - `Zend\Log\Processor\PsrPlaceholder` allows you to use PSR-3-compliant
    message placeholders in your log messages; they will be substituted from
    corresponding keys of values passed in the `$extra` array when logging the
    message.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.5.2 - 2015-07-06

### Added

- [#2](https://github.com/zendframework/zend-log/pull/2) adds
  the ability to specify the mail transport via the configuration options for a
  mail log writer, using the same format supported by
  `Zend\Mail\Transport\Factory::create()`; as an example:

  ```php
  $writer = new MailWriter([
      'mail' => [
          // message options
      ],
      'transport' => [
          'type' => 'smtp',
          'options' => [
               'host' => 'localhost',
          ],
      ],
  ]);
  ```

### Deprecated

- Nothing.

### Removed

- [#43](https://github.com/zendframework/zend-diactoros/pull/43) removed both
  `ServerRequestFactory::marshalUri()` and `ServerRequestFactory::marshalHostAndPort()`,
  which were deprecated prior to the 1.0 release.

### Fixed

- [#4](https://github.com/zendframework/zend-log/pull/4) adds better, more
  complete verbiage to the `composer.json` `suggest` section, to detail why
  and when you might need additional dependencies.
- [#1](https://github.com/zendframework/zend-log/pull/1) updates the code to
  remove conditionals related to PHP versions prior to PHP 5.5, and use bound
  closures in tests (not possible before 5.5).
