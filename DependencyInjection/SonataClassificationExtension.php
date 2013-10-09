<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 * SonataClassificationBundleExtension
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataClassificationExtension extends Extension
{
    /**
     * @throws \InvalidArgumentException
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('admin.xml');
        $loader->load('orm.xml');
        $loader->load('form.xml');

        $this->registerDoctrineMapping($config, $container);
        $this->configureClass($config, $container);
        $this->configureAdmin($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        // admin configuration
        $container->setParameter('sonata.classification.admin.tag.entity',        $config['class']['tag']);
        $container->setParameter('sonata.classification.admin.category.entity',   $config['class']['category']);
        $container->setParameter('sonata.classification.admin.collection.entity', $config['class']['collection']);

        // manager configuration
        $container->setParameter('sonata.classification.manager.tag.entity',        $config['class']['tag']);
        $container->setParameter('sonata.classification.manager.category.entity',   $config['class']['category']);
        $container->setParameter('sonata.classification.manager.collection.entity', $config['class']['collection']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureAdmin($config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.classification.admin.category.class',                $config['admin']['category']['class']);
        $container->setParameter('sonata.classification.admin.category.controller',           $config['admin']['category']['controller']);
        $container->setParameter('sonata.classification.admin.category.translation_domain',   $config['admin']['category']['translation']);

        $container->setParameter('sonata.classification.admin.tag.class',                     $config['admin']['tag']['class']);
        $container->setParameter('sonata.classification.admin.tag.controller',                $config['admin']['tag']['controller']);
        $container->setParameter('sonata.classification.admin.tag.translation_domain',        $config['admin']['tag']['translation']);

        $container->setParameter('sonata.classification.admin.collection.class',              $config['admin']['collection']['class']);
        $container->setParameter('sonata.classification.admin.collection.controller',         $config['admin']['collection']['controller']);
        $container->setParameter('sonata.classification.admin.collection.translation_domain', $config['admin']['collection']['translation']);
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {

        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['category'], 'mapOneToMany', array(
            'fieldName'     => 'children',
            'targetEntity'  => $config['class']['category'],
            'cascade'       => array(
                'remove',
                'persist',
            ),
            'mappedBy'      => 'parent',
            'orphanRemoval' => true,
            'orderBy'       => array(
                'position'  => 'ASC',
            ),
        ));

        $collector->addAssociation($config['class']['category'], 'mapManyToOne', array(
            'fieldName'     => 'parent',
            'targetEntity'  => $config['class']['category'],
            'cascade'       => array(
                'remove',
                'persist',
                'refresh',
                'merge',
                'detach',
            ),
            'mappedBy'      => NULL,
            'inversedBy'    => NULL,
            'joinColumns'   => array(
                array(
                 'name'     => 'parent_id',
                 'referencedColumnName' => 'id',
                 'onDelete' => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $collector->addAssociation($config['class']['collection'], 'mapManyToOne', array(
                'fieldName'     => 'media',
                'targetEntity'  => $config['class']['media'],
                'cascade'       => array(
                    'remove',
                    'persist',
                    'refresh',
                    'merge',
                    'detach',
                ),
                'mappedBy'      => NULL,
                'inversedBy'    => NULL,
                'joinColumns'   => array(
                    array(
                     'name'     => 'media_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'SET NULL',
                    ),
                ),
                'orphanRemoval' => false,
            ));

            $collector->addAssociation($config['class']['category'], 'mapManyToOne', array(
                'fieldName'     => 'media',
                'targetEntity'  => $config['class']['media'],
                'cascade'       => array(
                    'remove',
                    'persist',
                    'refresh',
                    'merge',
                    'detach',
                ),
                'mappedBy'      => NULL,
                'inversedBy'    => NULL,
                'joinColumns'   => array(
                    array(
                     'name'     => 'media_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'SET NULL',
                    ),
                ),
                'orphanRemoval' => false,
            ));
        }
    }
}
