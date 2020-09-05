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
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector as DeprecatedDoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * SonataClassificationBundleExtension.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataClassificationExtension extends Extension
{
    /**
     * @throws \InvalidArgumentException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('orm.xml');
        $loader->load('form.xml');
        $loader->load('serializer.xml');
        $loader->load('command.xml');

        if (isset($bundles['FOSRestBundle'], $bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('api_form.xml');
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
        }

        if (isset($bundles['SonataDoctrineBundle'])) {
            $this->registerSonataDoctrineMapping($config);
        } else {
            // NEXT MAJOR: Remove next line and throw error when not registering SonataDoctrineBundle
            $this->registerDoctrineMapping($config);
        }

        $this->configureClass($config, $container);
        $this->configureAdmin($config, $container);
    }

    /**
     * @param array $config
     */
    public function configureClass($config, ContainerBuilder $container)
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
     * @param array $config
     */
    public function configureAdmin($config, ContainerBuilder $container)
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
     * NEXT_MAJOR: Remove this method.
     */
    public function registerDoctrineMapping(array $config)
    {
        @trigger_error(
            'Using SonataEasyExtendsBundle is deprecated since sonata-project/classification-bundle 3.13. Please register SonataDoctrineBundle as a bundle instead.',
            E_USER_DEPRECATED
        );

        foreach ($config['class'] as $type => $class) {
            if ('media' !== $type && !class_exists($class)) {
                return;
            }
        }

        $collector = DeprecatedDoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['category'], 'mapOneToMany', [
            'fieldName' => 'children',
            'targetEntity' => $config['class']['category'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => 'parent',
            'orphanRemoval' => true,
            'orderBy' => [
                'position' => 'ASC',
            ],
        ]);

        $collector->addAssociation($config['class']['category'], 'mapManyToOne', [
            'fieldName' => 'parent',
            'targetEntity' => $config['class']['category'],
            'cascade' => [
                'persist',
                'refresh',
                'merge',
                'detach',
            ],
            'mappedBy' => null,
            'inversedBy' => 'children',
            'joinColumns' => [
                [
                 'name' => 'parent_id',
                 'referencedColumnName' => 'id',
                 'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['category'], 'mapManyToOne', [
            'fieldName' => 'context',
            'targetEntity' => $config['class']['context'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [
                [
                    'name' => 'context',
                    'referencedColumnName' => 'id',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['tag'], 'mapManyToOne', [
            'fieldName' => 'context',
            'targetEntity' => $config['class']['context'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [
                [
                    'name' => 'context',
                    'referencedColumnName' => 'id',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addUnique($config['class']['tag'], 'tag_context', ['slug', 'context']);

        $collector->addAssociation($config['class']['collection'], 'mapManyToOne', [
            'fieldName' => 'context',
            'targetEntity' => $config['class']['context'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [
                [
                    'name' => 'context',
                    'referencedColumnName' => 'id',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addUnique($config['class']['collection'], 'tag_collection', ['slug', 'context']);

        if (null !== $config['class']['media']) {
            $collector->addAssociation($config['class']['collection'], 'mapManyToOne', [
                'fieldName' => 'media',
                'targetEntity' => $config['class']['media'],
                'cascade' => [
                    'persist',
                ],
                'mappedBy' => null,
                'inversedBy' => null,
                'joinColumns' => [
                    [
                     'name' => 'media_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'SET NULL',
                    ],
                ],
                'orphanRemoval' => false,
            ]);

            $collector->addAssociation($config['class']['category'], 'mapManyToOne', [
                'fieldName' => 'media',
                'targetEntity' => $config['class']['media'],
                'cascade' => [
                    'persist',
                ],
                'mappedBy' => null,
                'inversedBy' => null,
                'joinColumns' => [
                    [
                     'name' => 'media_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'SET NULL',
                    ],
                ],
                'orphanRemoval' => false,
            ]);
        }
    }

    private function registerSonataDoctrineMapping(array $config): void
    {
        foreach ($config['class'] as $type => $class) {
            if ('media' !== $type && !class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation(
            $config['class']['category'],
            'mapOneToMany',
            OptionsBuilder::createOneToMany('children', $config['class']['category'])
                ->cascade(['persist'])
                ->mappedBy('parent')
                ->orphanRemoval()
                ->addOrder('position', 'ASC')
        );

        $collector->addAssociation(
            $config['class']['category'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('parent', $config['class']['category'])
                ->cascade(['persist', 'refresh', 'merge', 'detach'])
                ->inversedBy('children')
                ->addJoin([
                    'name' => 'parent_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ])
        );

        $contextOptions = OptionsBuilder::createManyToOne('context', $config['class']['context'])
            ->cascade(['persist'])
            ->addJoin([
                'name' => 'context',
                'referencedColumnName' => 'id',
            ]);

        $collector->addAssociation($config['class']['category'], 'mapManyToOne', $contextOptions);
        $collector->addAssociation($config['class']['tag'], 'mapManyToOne', $contextOptions);
        $collector->addAssociation($config['class']['collection'], 'mapManyToOne', $contextOptions);

        $collector->addUnique($config['class']['tag'], 'tag_context', ['slug', 'context']);
        $collector->addUnique($config['class']['collection'], 'tag_collection', ['slug', 'context']);

        if (null !== $config['class']['media']) {
            $mediaOptions = OptionsBuilder::createManyToOne('media', $config['class']['media'])
                ->cascade(['persist'])
                ->addJoin([
                    'name' => 'media_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'SET NULL',
                ]);

            $collector->addAssociation($config['class']['collection'], 'mapManyToOne', $mediaOptions);
            $collector->addAssociation($config['class']['category'], 'mapManyToOne', $mediaOptions);
        }
    }
}
