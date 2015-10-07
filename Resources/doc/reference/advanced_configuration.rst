.. index::
    single: Configuration

Advanced Configuration
======================

.. configuration-block::

    .. code-block:: yaml

        sonata_classification:
        class:
            tag:          Application\Sonata\ClassificationBundle\Entity\Tag
            category:     Application\Sonata\ClassificationBundle\Entity\Category
            collection:   Application\Sonata\ClassificationBundle\Entity\Collection
            media:        Application\Sonata\MediaBundle\Entity\Collection
            context:      Application\Sonata\ClassificationBundle\Entity\Collection

        admin:
            tag:
                class:        Sonata\ClassificationBundle\Admin\TagAdmin
                controller:   SonataAdminBundle:CRUD
                translation:  SonataClassificationBundle
            category:
                class:        Sonata\ClassificationBundle\Admin\CategoryAdmin
                controller:   SonataClassificationBundle:CategoryAdmin
                translation:  SonataClassificationBundle
            collection:
                class:        Sonata\ClassificationBundle\Admin\CollectionAdmin
                controller:   SonataAdminBundle:CRUD
                translation:  SonataClassificationBundle
            context:
                class:        Sonata\ClassificationBundle\Admin\ContextAdmin
                controller:   SonataAdminBundle:CRUD
                translation:  SonataClassificationBundle
