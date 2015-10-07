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
                    mappings:
                        SonataClassificationBundle: ~
                        #ApplicationSonataClassificationBundle: ~

* Import the ``sonata_classification.yml`` file in your main app/config/config.yml:

.. code-block:: yaml

    imports:
        #...
        - { resource: sonata_classification.yml }

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
