Admin
=====

The bundle comes with an Admin interface for managing your contexts, tags, collections and categories.

Removing the Admin 
------------------

There may be cases where you don't want the default admins to appear on your dashboard. For that, you need to set up a compiler pass that tags the services with ``show_in_dashboard = false``:


.. code-block:: php

  use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
  use Symfony\Component\DependencyInjection\ContainerBuilder;
  use Symfony\Component\DependencyInjection\Definition;
  
  class CompilerPass implements CompilerPassInterface
  {
      public function process(ContainerBuilder $container)
      {

          $definitionsNames = array('sonata.classification.admin.category', 'sonata.classification.admin.tag', 'sonata.classification.admin.context', 'sonata.classification.admin.collection');
  
          foreach ($definitionsNames as $definitionName) {
  
              /** @var Definition $definition */
              $definition = $container->getDefinition($definitionName);
  
              $tags = $definition->getTags();
  
              $tags['sonata.admin'][0]['show_in_dashboard'] = false;
              $definition->setTags($tags);
  
          }
      }
  }



Note that this will keep the services and the routes in the container, it will just not show links in the dashboard anymore.

If you don't want the admin routes to be accessible either, then you will have to remove the service definitions from the container.

Remove the definitions in your compiler pass:

.. code-block:: php

  $container->removeDefinition('sonata.media.admin.category');
  $container->removeDefinition('sonata.media.admin.tag');
  $container->removeDefinition('sonata.media.admin.context');
  $container->removeDefinition('sonata.media.admin.collection');
