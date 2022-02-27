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

use Sonata\ClassificationBundle\Admin\Filter\CategoryFilter;
use Sonata\ClassificationBundle\Admin\Filter\CollectionFilter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->parameters()

        ->set('sonata.classification.admin.groupname', 'sonata_classification')

        ->set('sonata.classification.admin.groupicon', "<i class='fa fa-tags'></i>");

    $containerConfigurator->services()

        ->set('sonata.classification.admin.category', '%sonata.classification.admin.category.class%')
            ->public()
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => '%sonata.classification.admin.groupname%',
                'translation_domain' => '%sonata.classification.admin.category.translation_domain%',
                'label' => 'label_categories',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => '%sonata.classification.admin.groupicon%',
            ])
            ->args([
                '',
                '%sonata.classification.admin.category.entity%',
                '%sonata.classification.admin.category.controller%',
                new ReferenceConfigurator('sonata.classification.manager.context'),
            ])
            ->call('setTemplates', [[
                'list' => '@SonataClassification/CategoryAdmin/list.html.twig',
                'tree' => '@SonataClassification/CategoryAdmin/tree.html.twig',
            ]])

        ->set('sonata.classification.admin.tag', '%sonata.classification.admin.tag.class%')
            ->public()
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => '%sonata.classification.admin.groupname%',
                'translation_domain' => '%sonata.classification.admin.tag.translation_domain%',
                'label' => 'label_tags',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => '%sonata.classification.admin.groupicon%',
            ])
            ->args([
                '',
                '%sonata.classification.admin.tag.entity%',
                '%sonata.classification.admin.tag.controller%',
                new ReferenceConfigurator('sonata.classification.manager.context'),
            ])

        ->set('sonata.classification.admin.collection', '%sonata.classification.admin.collection.class%')
            ->public()
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => '%sonata.classification.admin.groupname%',
                'translation_domain' => '%sonata.classification.admin.collection.translation_domain%',
                'label' => 'label_collections',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => '%sonata.classification.admin.groupicon%',
            ])
            ->args([
                '',
                '%sonata.classification.admin.collection.entity%',
                '%sonata.classification.admin.collection.controller%',
                new ReferenceConfigurator('sonata.classification.manager.context'),
            ])

        ->set('sonata.classification.admin.context', '%sonata.classification.admin.context.class%')
            ->public()
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => '%sonata.classification.admin.groupname%',
                'translation_domain' => '%sonata.classification.admin.context.translation_domain%',
                'label' => 'label_contexts',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => '%sonata.classification.admin.groupicon%',
            ])
            ->args([
                '',
                '%sonata.classification.admin.context.entity%',
                '%sonata.classification.admin.context.controller%',
            ])

        ->set(CategoryFilter::class)
            ->tag('sonata.admin.filter.type')
            ->args([
                new ReferenceConfigurator('sonata.classification.manager.category'),
            ])

        ->set(CollectionFilter::class)
            ->tag('sonata.admin.filter.type')
            ->args([
                new ReferenceConfigurator('sonata.classification.manager.collection'),
            ]);
};
