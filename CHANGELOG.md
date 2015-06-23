# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.5.2 - TBD

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

- Nothing.
