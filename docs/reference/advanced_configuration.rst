.. index::
    single: Configuration

Advanced Configuration
======================

.. configuration-block::

    .. code-block:: yaml

        sonata_classification:
            class:
                tag: App\Entity\SonataClassificationTag
                category: App\Entity\SonataClassificationCategory
                collection: App\Entity\SonataClassificationCollection
                context: App\Entity\SonataClassificationContext

            admin:
                tag:
                    class: Sonata\ClassificationBundle\Admin\TagAdmin
                    controller: SonataAdminBundle:CRUD
                    translation: SonataClassificationBundle
                category:
                    class: Sonata\ClassificationBundle\Admin\CategoryAdmin
                    controller: SonataClassificationBundle:CategoryAdmin
                    translation: SonataClassificationBundle
                collection:
                    class: Sonata\ClassificationBundle\Admin\CollectionAdmin
                    controller: SonataAdminBundle:CRUD
                    translation: SonataClassificationBundle
                context:
                    class: Sonata\ClassificationBundle\Admin\ContextAdmin
                    controller: SonataAdminBundle:CRUD
                    translation: SonataClassificationBundle
