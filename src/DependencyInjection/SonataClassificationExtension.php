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
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * SonataClassificationBundleExtension.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * @phpstan-import-type Config from Configuration
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

        /** @phpstan-var Config $config */
        $config = $processor->processConfiguration($configuration, $configs);

        /** @var array<string, mixed> $bundles */
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
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
     * @param array<string, mixed> $config
     *
     * @phpstan-param Config $config
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
     * @param array<string, mixed> $config
     *
     * @phpstan-param Config $config
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
     * @param array<string, mixed> $config
     *
     * @phpstan-param Config $config
     */
    private function registerSonataDoctrineMapping(array $config): void
    {
        foreach ($config['class'] as $class) {
            if (!class_exists($class)) {
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
    }
}
