<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\DependencyInjection;

use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * SonataClassificationBundleExtension.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class SonataClassificationExtension extends Extension
{
    /**
     * @throws \InvalidArgumentException
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        /** @var array<string, mixed> $bundles */
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('orm.php');
        $loader->load('form.php');
        $loader->load('command.php');
        $loader->load('controllers.php');

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.php');
        }

        if (isset($bundles['SonataDoctrineBundle'])) {
            $this->registerSonataDoctrineMapping($config);
        } else {
            throw new \Exception('You MUST register the SonataDoctrineBundle.');
        }

        $this->configureClass($config, $container);
        $this->configureAdmin($config, $container);
    }

    /**
     * @param array<string, array<string, string>> $config
     */
    private function configureClass(array $config, ContainerBuilder $container): void
    {
        // admin configuration
        $container->setParameter('sonata.classification.admin.tag.entity', $config['class']['tag']);
        $container->setParameter('sonata.classification.admin.category.entity', $config['class']['category']);
        $container->setParameter('sonata.classification.admin.collection.entity', $config['class']['collection']);
        $container->setParameter('sonata.classification.admin.context.entity', $config['class']['context']);

        // manager configuration
        $container->setParameter('sonata.classification.manager.tag.entity', $config['class']['tag']);
        $container->setParameter('sonata.classification.manager.category.entity', $config['class']['category']);
        $container->setParameter('sonata.classification.manager.collection.entity', $config['class']['collection']);
        $container->setParameter('sonata.classification.manager.context.entity', $config['class']['context']);
    }

    /**
     * @param array<string, array<string, array<string, string>>> $config
     */
    private function configureAdmin(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.classification.admin.category.class', $config['admin']['category']['class']);
        $container->setParameter('sonata.classification.admin.category.controller', $config['admin']['category']['controller']);
        $container->setParameter('sonata.classification.admin.category.translation_domain', $config['admin']['category']['translation']);

        $container->setParameter('sonata.classification.admin.tag.class', $config['admin']['tag']['class']);
        $container->setParameter('sonata.classification.admin.tag.controller', $config['admin']['tag']['controller']);
        $container->setParameter('sonata.classification.admin.tag.translation_domain', $config['admin']['tag']['translation']);

        $container->setParameter('sonata.classification.admin.collection.class', $config['admin']['collection']['class']);
        $container->setParameter('sonata.classification.admin.collection.controller', $config['admin']['collection']['controller']);
        $container->setParameter('sonata.classification.admin.collection.translation_domain', $config['admin']['collection']['translation']);

        $container->setParameter('sonata.classification.admin.context.class', $config['admin']['context']['class']);
        $container->setParameter('sonata.classification.admin.context.controller', $config['admin']['context']['controller']);
        $container->setParameter('sonata.classification.admin.context.translation_domain', $config['admin']['context']['translation']);
    }

    /**
     * @param array<string, array<string, string>> $config
     */
    private function registerSonataDoctrineMapping(array $config): void
    {
        $classes = $config['class'];
        foreach ($config['class'] as $class) {
            if (!class_exists($class)) {
                return;
            }
        }
        /** @var array<string, class-string> $classes */

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation(
            $classes['category'],
            'mapOneToMany',
            OptionsBuilder::createOneToMany('children', $classes['category'])
                ->cascade(['persist'])
                ->mappedBy('parent')
                ->orphanRemoval()
                ->addOrder('position', 'ASC')
        );

        $collector->addAssociation(
            $classes['category'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('parent', $classes['category'])
                ->cascade(['persist', 'refresh', 'merge', 'detach'])
                ->inversedBy('children')
                ->addJoin([
                    'name' => 'parent_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ])
        );

        $contextOptions = OptionsBuilder::createManyToOne('context', $classes['context'])
            ->cascade(['persist'])
            ->addJoin([
                'name' => 'context',
                'referencedColumnName' => 'id',
            ]);

        $collector->addAssociation($classes['category'], 'mapManyToOne', $contextOptions);
        $collector->addAssociation($classes['tag'], 'mapManyToOne', $contextOptions);
        $collector->addAssociation($classes['collection'], 'mapManyToOne', $contextOptions);

        $collector->addUnique($classes['tag'], 'tag_context', ['slug', 'context']);
        $collector->addUnique($classes['collection'], 'tag_collection', ['slug', 'context']);
    }
}
