.. index::
    single: Configuration

Advanced Configuration
======================

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
                controller: Sonata\AdminBundle\Controller\CRUDController
                translation: SonataClassificationBundle
            category:
                class: Sonata\ClassificationBundle\Admin\CategoryAdmin
                controller: Sonata\ClassificationBundle\Controller\CategoryAdminController
                translation: SonataClassificationBundle
            collection:
                class: Sonata\ClassificationBundle\Admin\CollectionAdmin
                controller: Sonata\AdminBundle\Controller\CRUDController
                translation: SonataClassificationBundle
            context:
                class: Sonata\ClassificationBundle\Admin\ContextAdmin
                controller: Sonata\AdminBundle\Controller\CRUDController
                translation: SonataClassificationBundle
