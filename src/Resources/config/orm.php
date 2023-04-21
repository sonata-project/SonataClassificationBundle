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

use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\ClassificationBundle\Entity\CollectionManager;
use Sonata\ClassificationBundle\Entity\ContextManager;
use Sonata\ClassificationBundle\Entity\TagManager;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()

        ->set('sonata.classification.manager.category.class', CategoryManager::class)

        ->set('sonata.classification.manager.tag.class', TagManager::class)

        ->set('sonata.classification.manager.collection.class', CollectionManager::class)

        ->set('sonata.classification.manager.context.class', ContextManager::class);

    $containerConfigurator->services()

        ->set('sonata.classification.manager.category', (string) param('sonata.classification.manager.category.class'))
            ->public()
            ->args([
                param('sonata.classification.manager.category.entity'),
                service('doctrine'),
                service('sonata.classification.manager.context'),
            ])

        ->set('sonata.classification.manager.tag', (string) param('sonata.classification.manager.tag.class'))
            ->public()
            ->args([
                param('sonata.classification.manager.tag.entity'),
                service('doctrine'),
            ])

        ->set('sonata.classification.manager.collection', (string) param('sonata.classification.manager.collection.class'))
            ->public()
            ->args([
                param('sonata.classification.manager.collection.entity'),
                service('doctrine'),
            ])

        ->set('sonata.classification.manager.context', (string) param('sonata.classification.manager.context.class'))
            ->public()
            ->args([
                param('sonata.classification.manager.context.entity'),
                service('doctrine'),
            ])

        ->alias(CategoryManagerInterface::class, 'sonata.classification.manager.category')
        ->alias(TagManagerInterface::class, 'sonata.classification.manager.tag')
        ->alias(CollectionManagerInterface::class, 'sonata.classification.manager.collection')
        ->alias(ContextManagerInterface::class, 'sonata.classification.manager.context');
};
