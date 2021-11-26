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

namespace Sonata\ClassificationBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\ClassificationBundle\Admin\CategoryAdmin;
use Sonata\ClassificationBundle\Admin\CollectionAdmin;
use Sonata\ClassificationBundle\Admin\ContextAdmin;
use Sonata\ClassificationBundle\Admin\Filter\CategoryFilter;
use Sonata\ClassificationBundle\Admin\Filter\CollectionFilter;
use Sonata\ClassificationBundle\Admin\TagAdmin;
use Sonata\ClassificationBundle\Command\FixContextCommand;
use Sonata\ClassificationBundle\DependencyInjection\SonataClassificationExtension;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\ClassificationBundle\Entity\CollectionManager;
use Sonata\ClassificationBundle\Entity\ContextManager;
use Sonata\ClassificationBundle\Entity\TagManager;
use Sonata\ClassificationBundle\Form\Type\CategorySelectorType;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;

final class SonataClassificationExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', [
            'SonataDoctrineBundle' => true,
            'SonataAdminBundle' => true,
        ]);
    }

    public function testLoadDefault(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('sonata.classification.admin.category', CategoryAdmin::class);
        $this->assertContainerBuilderHasService('sonata.classification.admin.tag', TagAdmin::class);
        $this->assertContainerBuilderHasService('sonata.classification.admin.collection', CollectionAdmin::class);
        $this->assertContainerBuilderHasService('sonata.classification.admin.context', ContextAdmin::class);
        $this->assertContainerBuilderHasService(CategoryFilter::class);
        $this->assertContainerBuilderHasService(CollectionFilter::class);
        $this->assertContainerBuilderHasService(FixContextCommand::class);
        $this->assertContainerBuilderHasService('sonata.classification.form.type.category_selector', CategorySelectorType::class);
        $this->assertContainerBuilderHasService('sonata.classification.manager.category', CategoryManager::class);
        $this->assertContainerBuilderHasService('sonata.classification.manager.tag', TagManager::class);
        $this->assertContainerBuilderHasService('sonata.classification.manager.collection', CollectionManager::class);
        $this->assertContainerBuilderHasService('sonata.classification.manager.context', ContextManager::class);
        $this->assertContainerBuilderHasService(CategoryManagerInterface::class);
        $this->assertContainerBuilderHasService(TagManagerInterface::class);
        $this->assertContainerBuilderHasService(CollectionManagerInterface::class);
        $this->assertContainerBuilderHasService(ContextManagerInterface::class);

        $this->assertContainerBuilderHasParameter('sonata.classification.admin.category.entity', 'Application\Sonata\ClassificationBundle\Entity\Category');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.category.class', CategoryAdmin::class);
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.category.controller', 'sonata.classification.controller.category_admin');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.category.translation_domain', 'SonataClassificationBundle');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.tag.entity', 'Application\Sonata\ClassificationBundle\Entity\Tag');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.tag.class', TagAdmin::class);
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.tag.controller', '%sonata.admin.configuration.default_controller%');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.tag.translation_domain', 'SonataClassificationBundle');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.collection.entity', 'Application\Sonata\ClassificationBundle\Entity\Collection');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.collection.class', CollectionAdmin::class);
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.collection.controller', '%sonata.admin.configuration.default_controller%');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.collection.translation_domain', 'SonataClassificationBundle');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.context.entity', 'Application\Sonata\ClassificationBundle\Entity\Context');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.context.class', ContextAdmin::class);
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.context.controller', '%sonata.admin.configuration.default_controller%');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.context.translation_domain', 'SonataClassificationBundle');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.groupname', 'sonata_classification');
        $this->assertContainerBuilderHasParameter('sonata.classification.admin.groupicon', "<i class='fa fa-tags'></i>");

        $this->assertContainerBuilderHasParameter('sonata.classification.manager.category.class', CategoryManager::class);
        $this->assertContainerBuilderHasParameter('sonata.classification.manager.tag.class', TagManager::class);
        $this->assertContainerBuilderHasParameter('sonata.classification.manager.collection.class', CollectionManager::class);
        $this->assertContainerBuilderHasParameter('sonata.classification.manager.context.class', ContextManager::class);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SonataClassificationExtension(),
        ];
    }
}
