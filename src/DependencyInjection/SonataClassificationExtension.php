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

    public function registerDoctrineMapping(array $config)
    {
        foreach ($config['class'] as $type => $class) {
            if ('media' !== $type && !class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();

        $categoryOnyToManyOptions = OptionsBuilder::create()
            ->add('fieldName', 'children')
            ->add('targetEntity', $config['class']['category'])
            ->add('cascade', [
                'persist',
            ])
            ->add('mappedBy', 'parent')
            ->add('orphanRemoval', true)
            ->add('orderBy', [
                'position' => 'ASC',
            ])
        ;

        $collector->addAssociation($config['class']['category'], 'mapOneToMany', $categoryOnyToManyOptions);

        $parentCategoryManyToOneOptions = OptionsBuilder::create()
            ->add('fieldName', 'parent')
            ->add('targetEntity', $config['class']['category'])
            ->add('cascade', [
                'persist',
                'refresh',
                'merge',
                'detach',
            ])
            ->add('mappedBy', null)
            ->add('inversedBy', 'children')
            ->add('joinColumns', [
                [
                    'name' => 'parent_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ])
            ->add('orphanRemoval', false)
        ;

        $collector->addAssociation($config['class']['category'], 'mapManyToOne', $parentCategoryManyToOneOptions);

        $contextManyToOneOptions = OptionsBuilder::create()
            ->add('fieldName', 'context')
            ->add('targetEntity', $config['class']['context'])
            ->add('cascade', [
                'persist',
            ])
            ->add('mappedBy', null)
            ->add('inversedBy', null)
            ->add('joinColumns', [
                [
                    'name' => 'context',
                    'referencedColumnName' => 'id',
                ],
            ])
            ->add('orphanRemoval', false)
        ;

        $collector->addAssociation($config['class']['category'], 'mapManyToOne', $contextManyToOneOptions);

        $tagManyToOneOptions = OptionsBuilder::create()
            ->add('fieldName', 'context')
            ->add('targetEntity', $config['class']['context'])
            ->add('cascade', [
                'persist',
            ])
            ->add('mappedBy', null)
            ->add('inversedBy', null)
            ->add('joinColumns', [
                [
                    'name' => 'context',
                    'referencedColumnName' => 'id',
                ],
            ])
            ->add('orphanRemoval', false)
        ;

        $collector->addAssociation($config['class']['tag'], 'mapManyToOne', $tagManyToOneOptions);

        $collector->addUnique($config['class']['tag'], 'tag_context', ['slug', 'context']);

        $collectionManyToOneOptions = OptionsBuilder::create()
            ->add('fieldName', 'context')
            ->add('targetEntity', $config['class']['context'])
            ->add('cascade', [
                'persist',
            ])
            ->add('mappedBy', null)
            ->add('inversedBy', null)
            ->add('joinColumns', [
                [
                    'name' => 'context',
                    'referencedColumnName' => 'id',
                ],
            ])
            ->add('orphanRemoval', false)
        ;

        $collector->addAssociation($config['class']['collection'], 'mapManyToOne', $collectionManyToOneOptions);

        $collector->addUnique($config['class']['collection'], 'tag_collection', ['slug', 'context']);

        if (null !== $config['class']['media']) {
            $mediaManyToOneOptions = OptionsBuilder::create()
                ->add('fieldName', 'media')
                ->add('targetEntity', $config['class']['media'])
                ->add('cascade', [
                    'persist',
                ])
                ->add('mappedBy', null)
                ->add('inversedBy', null)
                ->add('joinColumns', [
                    [
                        'name' => 'media_id',
                        'referencedColumnName' => 'id',
                        'onDelete' => 'SET NULL',
                    ],
                ])
                ->add('orphanRemoval', false)
            ;

            $collector->addAssociation($config['class']['collection'], 'mapManyToOne', $mediaManyToOneOptions);

            $categoryManyToOneOptions = OptionsBuilder::create()
                ->add('fieldName', 'media')
                ->add('targetEntity', $config['class']['media'])
                ->add('cascade', [
                    'persist',
                ])
                ->add('mappedBy', null)
                ->add('inversedBy', null)
                ->add('joinColumns', [
                    [
                        'name' => 'media_id',
                        'referencedColumnName' => 'id',
                        'onDelete' => 'SET NULL',
                    ],
                ])
                ->add('orphanRemoval', false)
            ;

            $collector->addAssociation($config['class']['category'], 'mapManyToOne', $categoryManyToOneOptions);
        }
    }
}
