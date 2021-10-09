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

use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\ClassificationBundle\Entity\CollectionManager;
use Sonata\ClassificationBundle\Entity\ContextManager;
use Sonata\ClassificationBundle\Entity\TagManager;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->parameters()

        ->set('sonata.classification.manager.category.class', CategoryManager::class)

        ->set('sonata.classification.manager.tag.class', TagManager::class)

        ->set('sonata.classification.manager.collection.class', CollectionManager::class)

        ->set('sonata.classification.manager.context.class', ContextManager::class);

    $containerConfigurator->services()

        ->set('sonata.classification.manager.category', '%sonata.classification.manager.category.class%')
            ->public()
            ->args([
                '%sonata.classification.manager.category.entity%',
                new ReferenceConfigurator('doctrine'),
                new ReferenceConfigurator('sonata.classification.manager.context'),
            ])

        ->set('sonata.classification.manager.tag', '%sonata.classification.manager.tag.class%')
            ->public()
            ->args([
                '%sonata.classification.manager.tag.entity%',
                new ReferenceConfigurator('doctrine'),
            ])

        ->set('sonata.classification.manager.collection', '%sonata.classification.manager.collection.class%')
            ->public()
            ->args([
                '%sonata.classification.manager.collection.entity%',
                new ReferenceConfigurator('doctrine'),
            ])

        ->set('sonata.classification.manager.context', '%sonata.classification.manager.context.class%')
            ->public()
            ->args([
                '%sonata.classification.manager.context.entity%',
                new ReferenceConfigurator('doctrine'),
            ])

        ->alias(CategoryManagerInterface::class, 'sonata.classification.manager.category')
        ->alias(TagManagerInterface::class, 'sonata.classification.manager.tag')
        ->alias(CollectionManagerInterface::class, 'sonata.classification.manager.collection')
        ->alias(ContextManagerInterface::class, 'sonata.classification.manager.context');
};
