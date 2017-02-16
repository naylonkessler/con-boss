# ConBoss

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cc7bb22a-38bc-40ac-8b59-2fb74ca4b50a/big.png)](https://insight.sensiolabs.com/projects/cc7bb22a-38bc-40ac-8b59-2fb74ca4b50a)

[![Build Status](https://travis-ci.org/naylonkessler/con-boss.svg?branch=master)](https://travis-ci.org/naylonkessler/con-boss)

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
// Get some bind name from container
$some = $container->get('name');

// Get some variable from container
$var = $container->get('$varName');

// Get multiple values from container
list($name, $var) = $container->get(['name', '$varName']);
```

You can also unbind something from container and check if some binding exists:

```php
// Unbind a name from container
$container->unbind('name');

// Check if some bind exists
$exists = $container->has('name');
```

Except for `Container::get()` and `Container::has()` methods that returns especific values you can take advantage of chained calls if you prefer:

```php
// Chained calls
$container->share('some-shared', Some\Shared::class)
    ->bind('some-bind', Some\Bind::class)
    ->bind('$someVar', 10);
```
