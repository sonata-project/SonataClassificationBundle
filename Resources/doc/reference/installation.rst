Installation
============

* Add SonataClassificationBundle to your vendor/bundles dir with the deps file

.. code-block:: php

    //composer.json
    "require": {
    //...
        "sonata-project/classification-bundle": "dev-master",
    //...
    }


* Add SonataClassificationBundle to your application kernel

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

* Create a configuration file : ``sonata_classification.yml``

.. code-block:: yaml

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

* import the ``sonata_classification.yml`` file and enable json type for doctrine

.. code-block:: yaml

    imports:
        #...
        - { resource: sonata_classification.yml }

    # ...
    doctrine:
        # ...
        types:
            json:     Sonata\Doctrine\Types\JsonType

* Run the easy-extends command

.. code-block:: shell

    php app/console sonata:easy-extends:generate --dest=src SonataClassificationBundle

* Enable the new bundles

.. code-block:: php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Application\Sonata\ClassificationBundle\SonataClassificationBundle(),
            // ...
        );
    }

.. code-block:: yaml

    sonata_classification:
        # ...

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataClassificationBundle: ~
                        # ...
