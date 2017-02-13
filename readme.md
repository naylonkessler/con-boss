# ConBoss

![SensioLabs Insight badge](https://insight.sensiolabs.com/projects/cc7bb22a-38bc-40ac-8b59-2fb74ca4b50a/big.png)

ConBoss is a light context manager (container) for using with dependency injection and inversion of control concepts. It is intended to be simple and light but still powerful.

## Installation

Just require the package using composer.

```sh
composer require naylonkessler/con-boss
```

## Using the package

Import and create a container:

```php
<?php

use ConBoss\Container;

$container = new Container();
```

After bind some dependencies:

```php
// Bind a name to a class FQN
$container->bind('name', Some\Class::class);

// Bind an interface FQN to a class FQN
$container->bind(Some\Interface::class, Some\Class::class);

// Bind an interface FQN to a factory closure
$container->bind(Some\Interface::class, function ($container) {
    return new \Some\Class();
});

// Bind a variable
$container->bind('$varName', 'Any content');

// Bind an interface FQN to a shared class FQN
$container->share(Shared\Interface::class, Some\Shared\Class::class);
```

And finally request some bind from container:

```php
// Return some bind name from container
$some = $container->get('name');

// Return some variable from container
$var = $container->get('$varName');
```
