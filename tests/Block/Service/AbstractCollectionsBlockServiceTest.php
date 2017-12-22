<?php

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
use Sonata\BlockBundle\Test\AbstractBlockServiceTestCase;
use Sonata\BlockBundle\Test\FakeTemplating;
use Sonata\ClassificationBundle\Admin\CollectionAdmin;
use Sonata\ClassificationBundle\Block\Service\AbstractCollectionsBlockService;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;

/**
 * @author Christian Gripp <mail@core23.de>
 */
final class AbstractCollectionsBlockServiceTest extends AbstractBlockServiceTestCase
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

    protected function setUp()
    {
        parent::setUp();

        $this->templating = new FakeTemplating();
        $this->contextManager = $this->createMock(ContextManagerInterface::class);
        $this->collectionManager = $this->createMock(CollectionManagerInterface::class);
        $this->collectionAdmin = $this->createMock(CollectionAdmin::class);
    }

    public function testDefaultSettings()
    {
        $blockService = $this->getMockForAbstractClass(AbstractCollectionsBlockService::class, [
            'block.service', $this->templating, $this->contextManager, $this->collectionManager, $this->collectionAdmin,
        ]);
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings([
            'title' => 'Collections',
            'collection' => false,
            'collectionId' => null,
            'context' => null,
            'template' => 'SonataClassificationBundle:Block:base_block_collections.html.twig',
        ], $blockContext);
    }

    public function testLoad()
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $collection->expects($this->any())->method('getId')->will($this->returnValue(23));

        $this->collectionManager->expects($this->any())
            ->method('find')
            ->with($this->equalTo('23'))
            ->will($this->returnValue($collection));

        $block = $this->createMock(BlockInterface::class);
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('collectionId'))
            ->will($this->returnValue(23));
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('collectionId'), $this->equalTo($collection));

        $blockService = $this->getMockForAbstractClass(AbstractCollectionsBlockService::class, [
            'block.service', $this->templating, $this->contextManager, $this->collectionManager, $this->collectionAdmin,
        ]);
        $blockService->load($block);
    }

    public function testPrePersist()
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $collection->expects($this->any())->method('getId')->will($this->returnValue(23));

        $block = $this->createMock(BlockInterface::class);
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('collectionId'))
            ->will($this->returnValue($collection));
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('collectionId'), $this->equalTo(23));

        $blockService = $this->getMockForAbstractClass(AbstractCollectionsBlockService::class, [
            'block.service', $this->templating, $this->contextManager, $this->collectionManager, $this->collectionAdmin,
        ]);
        $blockService->prePersist($block);
    }

    public function testPreUpdate()
    {
        $collection = $this->getMockBuilder(CollectionInterface::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $collection->expects($this->any())->method('getId')->will($this->returnValue(23));

        $block = $this->createMock(BlockInterface::class);
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('collectionId'))
            ->will($this->returnValue($collection));
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('collectionId'), $this->equalTo(23));

        $blockService = $this->getMockForAbstractClass(AbstractCollectionsBlockService::class, [
            'block.service', $this->templating, $this->contextManager, $this->collectionManager, $this->collectionAdmin,
        ]);
        $blockService->preUpdate($block);
    }
}
