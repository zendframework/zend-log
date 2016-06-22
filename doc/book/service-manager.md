# Service Manager

The `zend-log` package provides several components which can be used in 
combination with [zend-servicemanager](https://docs.zendframework.com/zend-servicemanager).
These components make it possible to quickly setup a logger instance or to
provide custom writers, filters, formatters, or processors.

## LoggerAbstractServiceFactory

When you register the abstract factory called `Zend\Log\LoggerAbstractServiceFactory`,
you will be able to setup loggers via the configuration. The abstract factory can be
registered in the service manager using the following configuration:

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

> ### Users of zend-component-installer
>
> If you are using zend-component-installer, you will have been prompted to
> install zend-log as a module or configuration provider when you installed
> zend-log. When you do, the abstract factory is automatically registered
> for you in your configuration.

> ### zend-log as a module
>
> If you are using zend-log v2.8 or later with a zend-mvc-based application,
> but not using zend-component-installer, you can register `Zend\Log` as a
> module in your application. When you do, the abstract service factory
> will be registered automatically.

Next, define your custom loggers in the configuration as follows:

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

The logger can now be retrieved via the service manager using the key used in
the configuration (`MyLogger`):

```php
/** @var \Zend\Log\Logger $logger */ 
$logger = $container->get('MyLogger');
```

## Custom Writers, Formatters, Filters, and Processors

In the `LoggerAbstractServiceFactory` example above, a custom formatter (called
`MyFormatter`) and a custom filter (called `MyFilter`) are used. These classes
need to be made available to the service manager. It's possible to do this via
custom plugin managers which have the names:

- `log_formatters`
- `log_filters`
- `log_processors`
- `log_writers`

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
