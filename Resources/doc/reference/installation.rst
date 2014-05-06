.. index::
    single: Introduction
    single: AppKernel

Installation
============

* Add ``SonataClassificationBundle`` to your vendor/bundles directory with the deps file:

.. code-block:: php

    // composer.json

    "require": {
    //...
        "sonata-project/classification-bundle": "dev-master",
    //...
    }


* Add ``SonataClassificationBundle`` to your application kernel:

.. code-block:: php

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new Sonata\ClassificationBundle\SonataClassificationBundle(),
            // ...
        );
    }

* Create a configuration file named ``sonata_classification.yml``:

.. code-block:: yaml

    # sonata_classification.yml

    sonata_classification:
        # ...

    doctrine:
        orm:
            entity_managers:
                default:
                    #metadata_cache_driver: apc
                    #query_cache_driver: apc
                    #result_cache_driver: apc
                    mappings:
                        #ApplicationSonataClassificationBundle: ~
                        SonataClassificationBundle: ~

* Import the ``sonata_classification.yml`` file and enable `json` type for doctrine:

.. code-block:: yaml

    imports:
        #...
        - { resource: sonata_classification.yml }

    # ...
    doctrine:
        # ...
        types:
            json:     Sonata\Doctrine\Types\JsonType

* Run the easy-extends command:

.. code-block:: bash

    php app/console sonata:easy-extends:generate --dest=src SonataClassificationBundle

* If necessary add the new namespace to the autoload:

.. code-block:: php

    // app/autoload.php

    $loader->add("Application", __DIR__.'/src/Application');

* Enable the new bundle:

.. code-block:: php

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle(),
            // ...
        );
    }

.. code-block:: yaml

    # sonata_classification.yml

    sonata_classification:
        # ...

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataClassificationBundle: ~
                        # ...
