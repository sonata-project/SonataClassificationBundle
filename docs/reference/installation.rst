.. index::
    single: Introduction
    single: AppKernel

Installation
============

Prerequisites
-------------

PHP 7.1 and Symfony >=3.4 or >= 4.2 are needed to make this bundle work.

Add ``SonataClassificationBundle`` via composer::

   composer require sonata-project/classification-bundle

Now, add the new ``SonataClassificationBundle`` to ``bundles.php`` file::

    // config/bundles.php

    return [
        // ...
        Sonata\ClassificationBundle\SonataClassificationBundle::class => ['all' => true],
    ];

If you are not using Symfony Flex, you should enable bundles in your ``AppKernel.php``::

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

Doctrine ORM Configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~
Add this bundle in the config mapping definition (or enable `auto_mapping`_)::

    # config/packages/doctrine.yaml

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        SonataClassificationBundle: ~

.. note::

    If you are not using Symfony Flex, this configuration should be added
    to ``app/config/config.yml``.

And then create the corresponding entities, ``src/Entity/Tag``::

    // src/Entity/Tag.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Entity\BaseTag;

    /**
     * @ORM\Entity
     * @ORM\Table(name="classification__tag")
     */
    class Tag extends BaseTag
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

``src/Entity/Category``::

    // src/Entity/Category.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Entity\BaseCategory;

    /**
     * @ORM\Entity
     * @ORM\Table(name="classification__category")
     */
    class Category extends BaseCategory
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

``src/Entity/Collection``::

    // src/Entity/Collection.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Entity\BaseCollection;

    /**
     * @ORM\Entity
     * @ORM\Table(name="classification__category")
     */
    class Collection extends BaseCollection
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

and ``src/Entity/Context``::

    // src/Entity/Context.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Entity\BaseContext;

    /**
     * @ORM\Entity
     * @ORM\Table(name="classification__category")
     */
    class Context extends BaseContext
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
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You have to create the corresponding documents, ``src/Document/Tag``::

    // src/Document/Tag.php

    use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
    use Sonata\ClassificationBundle\Document\BaseTag;

    /**
     * @ODM\Document
     */
    class Tag extends BaseTag
    {
        /**
         * @ODM\Id
         */
        protected $id;
    }

``src/Document/Category``::

    // src/Document/Category.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Document\BaseCategory;

    /**
     * @ORM\Document
     */
    class Category extends BaseCategory
    {
        /**
         * @ODM\Id
         */
        protected $id;
    }

``src/Document/Collection``::

    // src/Document/Collection.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Document\BaseCollection;

    /**
     * @ORM\Document
     */
    class Collection extends BaseCollection
    {
        /**
         * @ODM\Id
         */
        protected $id;
    }

and ``src/Document/Context``::

    // src/Document/Context.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ClassificationBundle\Document\BaseContext;

    /**
     * @ORM\Document
     */
    class Context extends BaseContext
    {
        /**
         * @ODM\Id
         */
        protected $id;
    }

And then configure ``ClassificationBundle`` to use the newly generated classes::

    # config/packages/sonata.yaml

    sonata_classification:
        class:
            tag: App\Entity\Tag
            category: App\Entity\Category
            collection: App\Entity\Collection
            context: App\Entity\Context

.. _`auto_mapping`: http://symfony.com/doc/4.4/reference/configuration/doctrine.html#configuration-overview
