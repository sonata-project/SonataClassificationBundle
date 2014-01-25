Advanced Configuration
======================

.. code-block:: yaml

  sonata_classification:
      class:
          tag:          Application\Sonata\ClassificationBundle\Entity\Tag
          category:     Application\Sonata\ClassificationBundle\Entity\Category
          collection:   Application\Sonata\ClassificationBundle\Entity\Collection
        
      admin:
          tag:
              class:        Sonata\ClassificationBundle\Admin\TagAdmin
              controller:   SonataAdminBundle:CRUD
              translation:  SonataClassificationBundle
          category:
              class:        Sonata\ClassificationBundle\Admin\CategoryAdmin
              controller:   SonataAdminBundle:CRUD
              translation:  SonataClassificationBundle
          collection:
              class:        Sonata\ClassificationBundle\Admin\CollectionAdmin
              controller:   SonataAdminBundle:CRUD
              translation:  SonataClassificationBundle

