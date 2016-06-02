# Service Manager

The `zend-log` package provides several components which can be used to
in combination with the service managaer. These components make it possible
to quickly setup a logger instance or to provide custom writers, filters,
formatters or processors.

## LoggerAbstractServiceFactory

When you register the abstract factory called `Zend\Log\LoggerAbstractServiceFactory`,
you will be able to setup loggers via the configuration. Simply register the 
abstract factory in the service manager like this:

```php
// module.config.php
return [
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
    ],
];
```

Next define your custom loggers in the configuration like this:

```php
// module.config.php
return [
    'log' => [
        'MyLogger' => [
            'writers' => [
                [
                    'name' => 'stream',
                    'priority' => Logger::DEBUG,
                    'options' => [
                        'stream' => 'php://output',
                        'formatter' => [
                            'name' => 'MyFormatter',
                        ],
                        'filters' => [
                            [
                                'name' => 'MyFilter',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

The logger can now be retrieved via the service manager:

```php
/** @var \Zend\Log\Logger $logger */ 
$logger = $serviceManager->get('MyLogger');
```

## Custom Writers, Formatters, Filters and Processors

In the example above about the `LoggerAbstractServiceFactory` abstract factory 
a custom formatter (called *MyFormatter*) and a custom filter (called *MyFilter*) 
is used. These classes need to be made available to the service manager. It's
possible to do this via custom plugin managers which have the names:

* log_formatters
* log_filters
* log_processors
* log_writers

### Example

```php
// module.config.php
return [
    'log_formatters' => [
        'factories' => [
            // ...
        ],
    ],
    'log_filters' => [
        'factories' => [
            // ...
        ],
    ],
    'log_processors' => [
        'factories' => [
            // ...
        ],
    ],
    'log_writers' => [
        'factories' => [
            // ...
        ],
    ],
];
```
