UPGRADE 3.x
===========

UPGRADE FROM 3.12 to 3.13
=========================

### SonataEasyExtends is deprecated

Registering `SonataEasyExtendsBundle` bundle is deprecated, it SHOULD NOT be registered.
Register `SonataDoctrineBundle` bundle instead.

UPGRADE FROM 3.1 to 3.2
=======================

### Deep validation

In `CategoryAdmin` and `CollectionAdmin`,
disabling deep validation by unsetting the `cascade_validation` option is now deprecated,
and should be done by overriding `getFormBuilder` instead.

### Tests

All files under the ``Tests`` directory are now correctly handled as internal test classes.
You can't extend them anymore, because they are only loaded when running internal tests.
More information can be found in the [composer docs](https://getcomposer.org/doc/04-schema.md#autoload-dev).
