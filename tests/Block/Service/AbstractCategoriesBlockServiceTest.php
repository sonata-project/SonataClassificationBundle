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
use Sonata\BlockBundle\Test\AbstractBlockServiceTestCase;
use Sonata\BlockBundle\Test\FakeTemplating;
use Sonata\ClassificationBundle\Admin\CategoryAdmin;
use Sonata\ClassificationBundle\Block\Service\AbstractCategoriesBlockService;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;

/**
 * @author Christian Gripp <mail@core23.de>
 */
final class AbstractCategoriesBlockServiceTest extends AbstractBlockServiceTestCase
{
    /**
     * @var ContextManagerInterface
     */
    private $contextManager;

    /**
     * @var CategoryManagerInterface
     */
    private $categoryManager;

    /**
     * @var CategoryAdmin
     */
    private $categoryAdmin;

    protected function setUp()
    {
        parent::setUp();

        $this->templating = new FakeTemplating();
        $this->contextManager = $this->createMock(ContextManagerInterface::class);
        $this->categoryManager = $this->createMock(CategoryManagerInterface::class);
        $this->categoryAdmin = $this->createMock(CategoryAdmin::class);
    }

    public function testDefaultSettings()
    {
        $blockService = $this->getMockForAbstractClass(AbstractCategoriesBlockService::class, [
            'block.service', $this->templating, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ]);
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings([
            'title' => null,
            'translation_domain' => null,
            'icon' => 'fa fa-folder-open-o',
            'class' => null,
            'category' => false,
            'categoryId' => null,
            'context' => 'default',
            'template' => '@SonataClassification/Block/base_block_categories.html.twig',
        ], $blockContext);
    }

    public function testLoad()
    {
        $category = $this->getMockBuilder(CategoryInterface::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $category->expects($this->any())->method('getId')->willReturn(23);

        $this->categoryManager->expects($this->any())
            ->method('find')
            ->with($this->equalTo('23'))
            ->willReturn($category);

        $block = $this->createMock(BlockInterface::class);
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('categoryId'))
            ->willReturn(23);
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('categoryId'), $this->equalTo($category));

        $blockService = $this->getMockForAbstractClass(AbstractCategoriesBlockService::class, [
            'block.service', $this->templating, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ]);
        $blockService->load($block);
    }

    public function testPrePersist()
    {
        $category = $this->getMockBuilder(CategoryInterface::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $category->expects($this->any())->method('getId')->willReturn(23);

        $block = $this->createMock(BlockInterface::class);
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('categoryId'))
            ->willReturn($category);
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('categoryId'), $this->equalTo(23));

        $blockService = $this->getMockForAbstractClass(AbstractCategoriesBlockService::class, [
            'block.service', $this->templating, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ]);
        $blockService->prePersist($block);
    }

    public function testPreUpdate()
    {
        $category = $this->getMockBuilder(CategoryInterface::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $category->expects($this->any())->method('getId')->willReturn(23);

        $block = $this->createMock(BlockInterface::class);
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('categoryId'))
            ->willReturn($category);
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('categoryId'), $this->equalTo(23));

        $blockService = $this->getMockForAbstractClass(AbstractCategoriesBlockService::class, [
            'block.service', $this->templating, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ]);
        $blockService->preUpdate($block);
    }
}
