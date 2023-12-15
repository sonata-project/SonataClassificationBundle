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

use Sonata\ClassificationBundle\Admin\CategoryAdmin;
use Sonata\ClassificationBundle\Admin\CollectionAdmin;
use Sonata\ClassificationBundle\Admin\ContextAdmin;
use Sonata\ClassificationBundle\Admin\TagAdmin;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @phpstan-type Config = array{
 *     class: array{
 *         category: class-string,
 *         collection: class-string,
 *         context: class-string,
 *         tag: class-string,
 *     },
 *     admin: array{
 *         category: array{class: class-string, controller: class-string, translation: string},
 *         collection: array{class: class-string, controller: class-string, translation: string},
 *         context: array{class: class-string, controller: class-string, translation: string},
 *         tag: array{class: class-string, controller: class-string, translation: string},
 *     },
 * }
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UndefinedInterfaceMethod
     *
     * @see https://github.com/psalm/psalm-plugin-symfony/issues/174
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sonata_classification');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('tag')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Tag')->end()
                        ->scalarNode('category')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Category')->end()
                        ->scalarNode('collection')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Collection')->end()
                        ->scalarNode('context')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Context')->end()
                    ->end()
                ->end()

                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue(CategoryAdmin::class)->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('sonata.classification.controller.category_admin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataClassificationBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('tag')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue(TagAdmin::class)->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('%sonata.admin.configuration.default_controller%')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataClassificationBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('collection')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue(CollectionAdmin::class)->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('%sonata.admin.configuration.default_controller%')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataClassificationBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('context')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue(ContextAdmin::class)->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('%sonata.admin.configuration.default_controller%')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataClassificationBundle')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
