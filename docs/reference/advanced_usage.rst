.. index::
    single: Configuration

Advanced usage
==============

Override Tree template
----------------------

By default, the ``SonataClassificationBundle`` uses its own view template for the admin tree of categories.
You can easily customize it with the ``SonataAdminBundle`` configuration :

    .. code-block:: yaml

        sonata_admin:
            admin_services:
                sonata.classification.admin.category:
                    templates:
                        view:
                            tree: 'AppBundle:CategoryAdmin:tree.html.twig'

