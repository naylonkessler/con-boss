# ConBoss specification

This file describes all features present in ConBoss.

## Binding

The binding is used to associate an abstraction with the appropriate resolution.
Is commonly used to associate an interface with a concret class.

### Features

- Bind a name to a fully qualified class name;
- Bind a fully qualified interface name to a fully qualified class name;
- Bind a name to a closure;
- Bind a fully qualified interface name to a closure;
- Bind a variable.

## Sharing

The sharing is like a binding but its always uses a single instance when resolving the binding.

### Features

- Bind a shared name to a fully qualified class name;
- Bind a shared fully qualified interface name to a fully qualified class name;
- Bind a shared name to a closure;
- Bind a shared fully qualified interface name to a closure.

## Resolving

The resolving is the process that dependencies are resolved to return a concret and complete instance.

### Features

- Resolve a bind chain;
- Return a single instance;
- Return multiple instances;
- Return a variable.
