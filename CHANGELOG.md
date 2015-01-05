CHANGELOG
=========

### 2014-19-25

* add Context model to allow better classification. You can now have the 2 tags with the same slug, but with different context.
  You can run the ``sonata:classification:fix-context`` to add missing default context after you upgrade your schema.
* you also need to re-run the easy-extends command to generate the new ``Context`` model.

### 2012-09-24

* changed service parameters into options that come through the configuration with the old values as the new defaults.
