.. index::
    single: Introduction
    single: AppKernel

Installation
============

Prerequisites
-------------

PHP 7.1 and Symfony >=3.4 or >= 4.2 are needed to make this bundle work, there are
also some Sonata dependencies that need to be installed and configured beforehand:

* `SonataEasyExtendsBundle <https://sonata-project.org/bundles/easy-extends>`_

Add ``SonataClassificationBundle`` via composer:

.. code-block:: bash

   composer require sonata-project/classification-bundle

Now, add the new ``SonataClassificationBundle`` to ``bundles.php`` file::


    // config/bundles.php

    return [
        // ...
        Sonata\ClassificationBundle\SonataClassificationBundle::class => ['all' => true],
    ];

.. note::

    If you are not using Symfony Flex, you should enable bundles in your
    ``AppKernel.php``.

.. code-block:: php

    // app/AppKernel.php

    public function registerBundles()
    {
        return [
            new Sonata\ClassificationBundle\SonataClassificationBundle(),
            // ...
        ];
    }

Configuration
-------------

Doctrine Configuration
~~~~~~~~~~~~~~~~~~~~~~
Add these bundles in the config mapping definition (or enable `auto_mapping`_):

.. code-block:: yaml

    # config/packages/doctrine.yaml

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataClassificationBundle: ~
                        SonataClassificationBundle: ~

.. note::

    If you are not using Symfony Flex, this configuration should be added
    to ``app/config/config.yml``.

Extending the Bundle
--------------------
At this point, the bundle is functional, but not quite ready yet. You need to
generate the correct entities for the media:

.. code-block:: bash

    bin/console sonata:easy-extends:generate SonataClassificationBundle --dest=src --namespace_prefix=App

.. note::

    If you are not using Symfony Flex, use command without ``--namespace_prefix=App``.

With provided parameters, the files are generated in ``src/Application/Sonata/ClassificationBundle``.

.. note::

    The command will generate domain objects in ``App\Application`` namespace.
    So you can point entities' associations to a global and common namespace.
    This will make Entities sharing easier as your models will allow to
    point to a global namespace. For instance the tag will be
    ``App\Application\Sonata\ClassificationBundle\Entity\Tag``.

.. note::

    If you are not using Symfony Flex, the namespace will be ``Application\Sonata\ClassificationBundle\Entity``.

Now, add the new ``Application`` Bundle into the ``bundles.php``::

    // config/bundles.php

    return [
        // ...
        App\Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle::class => ['all' => true],
    ];

.. note::

    If you are not using Symfony Flex, add the new ``Application`` Bundle into your
    ``AppKernel.php``.

.. code-block:: php

    // app/AppKernel.php

    class AppKernel {

        public function registerBundles()
        {
            return [
                // Application Bundles
                // ...
                new Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle(),
                // ...
            ];
        }
    }

And configure ``ClassificationBundle`` to use the newly generated classes:

.. code-block:: yaml

    # config/packages/sonata.yaml

    sonata_classification:
        class:
            tag: App\Application\Sonata\ClassificationBundle\Entity\Tag
            category: App\Application\Sonata\ClassificationBundle\Entity\Category
            collection: App\Application\Sonata\ClassificationBundle\Entity\Collection
            context: App\Application\Sonata\ClassificationBundle\Entity\Context


.. note::

    If you are not using Symfony Flex, add classes without the ``App\``
    part and this configuration should be added to ``app/config/config.yml``

The only thing left is to update your schema:

.. code-block:: bash

    bin/console doctrine:schema:update --force

.. _`auto_mapping`: http://symfony.com/doc/2.0/reference/configuration/doctrine.html#configuration-overview
