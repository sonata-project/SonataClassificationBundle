.. index::
    single: Installation
    single: Configuration

Installation
============

Prerequisites
-------------

PHP ^7.2 and Symfony ^4.4 are needed to make this bundle work, there are
also some Sonata dependencies that need to be installed and configured beforehand.

Optional dependencies:

* `SonataAdminBundle <https://docs.sonata-project.org/projects/SonataAdminBundle/en/3.x/>`_
* `SonataBlockBundle <https://docs.sonata-project.org/projects/SonataBlockBundle/en/3.x/>`_
* `SonataMediaBundle <https://docs.sonata-project.org/projects/SonataMediaBundle/en/3.x/>`_

And the persistence bundle (choose one):

* `SonataDoctrineOrmAdminBundle <https://docs.sonata-project.org/projects/SonataDoctrineORMAdminBundle/en/3.x/>`_
* `SonataDoctrineMongoDBAdminBundle <https://docs.sonata-project.org/projects/SonataDoctrineMongoDBAdminBundle/en/3.x/>`_

Follow also their configuration step; you will find everything you need in
their own installation chapter.

.. note::

    If a dependency is already installed somewhere in your project or in
    another dependency, you won't need to install it again.

Enable the Bundle
-----------------

Add ``SonataClassificationBundle`` via composer::

    composer require sonata-project/classification-bundle

If you want to use the REST API, you also need ``friendsofsymfony/rest-bundle`` and ``nelmio/api-doc-bundle``::

    composer require friendsofsymfony/rest-bundle nelmio/api-doc-bundle

Next, be sure to enable the bundles in your ``config/bundles.php`` file if they
are not already enabled::

    // config/bundles.php

    return [
        // ...
        Sonata\ClassificationBundle\SonataClassificationBundle::class => ['all' => true],
    ];

Configuration
=============

SonataClassificationBundle Configuration
----------------------------------------

.. code-block:: yaml

    # config/packages/sonata_classification.yaml

    sonata_classification:
        class:
            tag: App\Entity\SonataClassificationTag
            category: App\Entity\SonataClassificationCategory
            collection: App\Entity\SonataClassificationCollection
            context: App\Entity\SonataClassificationContext

Doctrine ORM Configuration
--------------------------

Add these bundles in the config mapping definition (or enable `auto_mapping`_)::

    # config/packages/doctrine.yaml

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        SonataClassificationBundle: ~

And then create the corresponding entities, ``src/Entity/SonataClassificationTag``::

    // src/Entity/SonataClassificationTag.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Entity\BaseTag;

    /**
     * @ORM\Entity
     * @ORM\Table(name="classification__tag")
     */
    class SonataClassificationTag extends BaseTag
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

``src/Entity/SonataClassificationCategory``::

    // src/Entity/SonataClassificationCategory.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Entity\BaseCategory;

    /**
     * @ORM\Entity
     * @ORM\Table(name="classification__category")
     */
    class SonataClassificationCategory extends BaseCategory
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

``src/Entity/SonataClassificationCollection``::

    // src/Entity/SonataClassificationCollection.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Entity\BaseCollection;

    /**
     * @ORM\Entity
     * @ORM\Table(name="classification__collection")
     */
    class SonataClassificationCollection extends BaseCollection
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

and ``src/Entity/SonataClassificationContext``::

    // src/Entity/SonataClassificationContext.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Entity\BaseContext;

    /**
     * @ORM\Entity
     * @ORM\Table(name="classification__context")
     */
    class SonataClassificationContext extends BaseContext
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

The only thing left is to update your schema::

    bin/console doctrine:schema:update --force

Doctrine MongoDB Configuration
------------------------------

You have to create the corresponding documents, ``src/Document/SonataClassificationTag``::

    // src/Document/SonataClassificationTag.php

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Sonata\ClassificationBundle\Document\BaseTag;

    /**
     * @MongoDB\Document
     */
    class SonataClassificationTag extends BaseTag
    {
        /**
         * @MongoDB\Id
         */
        protected $id;
    }

``src/Document/SonataClassificationCategory``::

    // src/Document/SonataClassificationCategory.php

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Sonata\ClassificationBundle\Document\BaseCategory;

    /**
     * @MongoDB\Document
     */
    class SonataClassificationCategory extends BaseCategory
    {
        /**
         * @MongoDB\Id
         */
        protected $id;
    }

``src/Document/SonataClassificationCollection``::

    // src/Document/SonataClassificationCollection.php

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Sonata\ClassificationBundle\Document\BaseCollection;

    /**
     * @MongoDB\Document
     */
    class SonataClassificationCollection extends BaseCollection
    {
        /**
         * @MongoDB\Id
         */
        protected $id;
    }

and ``src/Document/SonataClassificationContext``::

    // src/Document/SonataClassificationContext.php

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Sonata\ClassificationBundle\Document\BaseContext;

    /**
     * @MongoDB\Document
     */
    class SonataClassificationContext extends BaseContext
    {
        /**
         * @MongoDB\Id
         */
        protected $id;
    }

And then configure ``ClassificationBundle`` to use the newly generated classes::

    # config/packages/sonata_classification.yaml

    sonata_classification:
        class:
            tag: App\Document\SonataClassificationTag
            category: App\Document\SonataClassificationCategory
            collection: App\Document\SonataClassificationCollection
            context: App\Document\SonataClassificationContext

Next Steps
----------

At this point, your Symfony installation should be fully functional, without errors
showing up from SonataClassificationBundle. If, at this point or during the installation,
you come across any errors, don't panic:

    - Read the error message carefully. Try to find out exactly which bundle is causing the error.
      Is it SonataClassificationBundle or one of the dependencies?
    - Make sure you followed all the instructions correctly, for both SonataClassificationBundle and its dependencies.
    - Still no luck? Try checking the project's `open issues on GitHub`_.

.. _`open issues on GitHub`: https://github.com/sonata-project/SonataClassificationBundle/issues
.. _`auto_mapping`: http://symfony.com/doc/4.4/reference/configuration/doctrine.html#configuration-overviews
