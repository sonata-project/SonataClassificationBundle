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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sonata\ClassificationBundle\Admin\Filter\CategoryFilter;
use Sonata\ClassificationBundle\Admin\Filter\CollectionFilter;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()

        ->set('sonata.classification.admin.groupname', 'sonata_classification')

        ->set('sonata.classification.admin.groupicon', "<i class='fa fa-tags'></i>");

    $containerConfigurator->services()

        ->set('sonata.classification.admin.category', (string) param('sonata.classification.admin.category.class'))
            ->public()
            ->tag('sonata.admin', [
                'model_class' => (string) param('sonata.classification.admin.category.entity'),
                'controller' => (string) param('sonata.classification.admin.category.controller'),
                'manager_type' => 'orm',
                'group' => (string) param('sonata.classification.admin.groupname'),
                'translation_domain' => (string) param('sonata.classification.admin.category.translation_domain'),
                'label' => 'label_categories',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => (string) param('sonata.classification.admin.groupicon'),
            ])
            ->args([
                service('sonata.classification.manager.context'),
            ])
            ->call('setTemplates', [[
                'list' => '@SonataClassification/CategoryAdmin/list.html.twig',
                'tree' => '@SonataClassification/CategoryAdmin/tree.html.twig',
            ]])

        ->set('sonata.classification.admin.tag', (string) param('sonata.classification.admin.tag.class'))
            ->public()
            ->tag('sonata.admin', [
                'model_class' => (string) param('sonata.classification.admin.tag.entity'),
                'controller' => (string) param('sonata.classification.admin.tag.controller'),
                'manager_type' => 'orm',
                'group' => (string) param('sonata.classification.admin.groupname'),
                'translation_domain' => (string) param('sonata.classification.admin.tag.translation_domain'),
                'label' => 'label_tags',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => (string) param('sonata.classification.admin.groupicon'),
            ])
            ->args([
                service('sonata.classification.manager.context'),
            ])

        ->set('sonata.classification.admin.collection', (string) param('sonata.classification.admin.collection.class'))
            ->public()
            ->tag('sonata.admin', [
                'model_class' => (string) param('sonata.classification.admin.collection.entity'),
                'controller' => (string) param('sonata.classification.admin.collection.controller'),
                'manager_type' => 'orm',
                'group' => (string) param('sonata.classification.admin.groupname'),
                'translation_domain' => (string) param('sonata.classification.admin.collection.translation_domain'),
                'label' => 'label_collections',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => (string) param('sonata.classification.admin.groupicon'),
            ])
            ->args([
                service('sonata.classification.manager.context'),
            ])

        ->set('sonata.classification.admin.context', (string) param('sonata.classification.admin.context.class'))
            ->public()
            ->tag('sonata.admin', [
                'model_class' => (string) param('sonata.classification.admin.context.entity'),
                'controller' => (string) param('sonata.classification.admin.context.controller'),
                'manager_type' => 'orm',
                'group' => (string) param('sonata.classification.admin.groupname'),
                'translation_domain' => (string) param('sonata.classification.admin.context.translation_domain'),
                'label' => 'label_contexts',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => (string) param('sonata.classification.admin.groupicon'),
            ])

        ->set(CategoryFilter::class)
            ->tag('sonata.admin.filter.type')
            ->args([
                service('sonata.classification.manager.category'),
            ])

        ->set(CollectionFilter::class)
            ->tag('sonata.admin.filter.type')
            ->args([
                service('sonata.classification.manager.collection'),
            ]);
};
