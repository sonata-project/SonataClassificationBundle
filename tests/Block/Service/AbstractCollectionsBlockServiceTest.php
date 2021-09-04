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

namespace Sonata\ClassificationBundle\Tests\Block\Service;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Test\BlockServiceTestCase;
use Sonata\ClassificationBundle\Admin\CollectionAdmin;
use Sonata\ClassificationBundle\Block\Service\AbstractCollectionsBlockService;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Twig\Environment;

/**
 * @author Christian Gripp <mail@core23.de>
 */
final class AbstractCollectionsBlockServiceTest extends BlockServiceTestCase
{
    /**
     * @var ContextManagerInterface
     */
    private $contextManager;

    /**
     * @var CollectionManagerInterface
     */
    private $collectionManager;

    /**
     * @var CollectionAdmin
     */
    private $collectionAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->twig = $this->createMock(Environment::class);
        $this->contextManager = $this->createMock(ContextManagerInterface::class);
        $this->collectionManager = $this->createMock(CollectionManagerInterface::class);
        $this->collectionAdmin = $this->createMock(CollectionAdmin::class);
    }

    public function testDefaultSettings(): void
    {
        $blockService = $this->getMockForAbstractClass(AbstractCollectionsBlockService::class, [
            $this->twig, $this->contextManager, $this->collectionManager, $this->collectionAdmin,
        ]);
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings([
            'title' => null,
            'translation_domain' => null,
            'icon' => 'fa fa-inpanel',
            'class' => null,
            'collection' => false,
            'collectionId' => null,
            'context' => null,
            'template' => '@SonataClassification/Block/base_block_collections.html.twig',
        ], $blockContext);
    }

    public function testLoad(): void
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $collection->expects(static::any())->method('getId')->willReturn(23);

        $this->collectionManager->expects(static::any())
            ->method('find')
            ->with(static::equalTo('23'))
            ->willReturn($collection);

        $block = $this->createMock(BlockInterface::class);
        $block->expects(static::any())
            ->method('getSetting')
            ->with(static::equalTo('collectionId'))
            ->willReturn(23);
        $block->expects(static::once())
            ->method('setSetting')
            ->with(static::equalTo('collectionId'), static::equalTo($collection));

        $blockService = $this->getMockForAbstractClass(AbstractCollectionsBlockService::class, [
            $this->twig, $this->contextManager, $this->collectionManager, $this->collectionAdmin,
        ]);
        $blockService->load($block);
    }

    public function testPrePersist(): void
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $collection->expects(static::any())->method('getId')->willReturn(23);

        $block = $this->createMock(BlockInterface::class);
        $block->expects(static::any())
            ->method('getSetting')
            ->with(static::equalTo('collectionId'))
            ->willReturn($collection);
        $block->expects(static::once())
            ->method('setSetting')
            ->with(static::equalTo('collectionId'), static::equalTo(23));

        $blockService = $this->getMockForAbstractClass(AbstractCollectionsBlockService::class, [
            $this->twig, $this->contextManager, $this->collectionManager, $this->collectionAdmin,
        ]);
        $blockService->prePersist($block);
    }

    public function testPreUpdate(): void
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $collection->expects(static::any())->method('getId')->willReturn(23);

        $block = $this->createMock(BlockInterface::class);
        $block->expects(static::any())
            ->method('getSetting')
            ->with(static::equalTo('collectionId'))
            ->willReturn($collection);
        $block->expects(static::once())
            ->method('setSetting')
            ->with(static::equalTo('collectionId'), static::equalTo(23));

        $blockService = $this->getMockForAbstractClass(AbstractCollectionsBlockService::class, [
            $this->twig, $this->contextManager, $this->collectionManager, $this->collectionAdmin,
        ]);
        $blockService->preUpdate($block);
    }
}
