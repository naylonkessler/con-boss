# ConBoss

ConBoss is a light context manager (container) for using with dependency injection and inversion of control concepts. It is intended to be simple and light but still powerful.

```php
<?php

use ConBoss\Container;

$container = new Container();
$container->bind('name', '\Some\Class');
$container->bind('\Some\Interface', '\Some\Class');
$container->bind('\Another\Interface', function ($container) {
    return new \Some\Class();
});
$container->share('\Shared\Interface', '\Some\Shared\Class');

$some = $container->get('name');
```