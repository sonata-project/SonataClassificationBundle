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
use Sonata\ClassificationBundle\Admin\CategoryAdmin;
use Sonata\ClassificationBundle\Block\Service\AbstractCategoriesBlockService;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Twig\Environment;

/**
 * @author Christian Gripp <mail@core23.de>
 */
final class AbstractCategoriesBlockServiceTest extends BlockServiceTestCase
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->twig = $this->createMock(Environment::class);
        $this->contextManager = $this->createMock(ContextManagerInterface::class);
        $this->categoryManager = $this->createMock(CategoryManagerInterface::class);
        $this->categoryAdmin = $this->createMock(CategoryAdmin::class);
    }

    public function testDefaultSettings(): void
    {
        $blockService = $this->getMockForAbstractClass(AbstractCategoriesBlockService::class, [
            $this->twig, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
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

    public function testLoad(): void
    {
        $category = $this->getMockBuilder(CategoryInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $category->expects(static::any())->method('getId')->willReturn(23);

        $this->categoryManager->expects(static::any())
            ->method('find')
            ->with(static::equalTo('23'))
            ->willReturn($category);

        $block = $this->createMock(BlockInterface::class);
        $block->expects(static::any())
            ->method('getSetting')
            ->with(static::equalTo('categoryId'))
            ->willReturn(23);
        $block->expects(static::once())
            ->method('setSetting')
            ->with(static::equalTo('categoryId'), static::equalTo($category));

        $blockService = $this->getMockForAbstractClass(AbstractCategoriesBlockService::class, [
            $this->twig, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ]);
        $blockService->load($block);
    }

    public function testPrePersist(): void
    {
        $category = $this->getMockBuilder(CategoryInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $category->expects(static::any())->method('getId')->willReturn(23);

        $block = $this->createMock(BlockInterface::class);
        $block->expects(static::any())
            ->method('getSetting')
            ->with(static::equalTo('categoryId'))
            ->willReturn($category);
        $block->expects(static::once())
            ->method('setSetting')
            ->with(static::equalTo('categoryId'), static::equalTo(23));

        $blockService = $this->getMockForAbstractClass(AbstractCategoriesBlockService::class, [
            $this->twig, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ]);
        $blockService->prePersist($block);
    }

    public function testPreUpdate(): void
    {
        $category = $this->getMockBuilder(CategoryInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $category->expects(static::any())->method('getId')->willReturn(23);

        $block = $this->createMock(BlockInterface::class);
        $block->expects(static::any())
            ->method('getSetting')
            ->with(static::equalTo('categoryId'))
            ->willReturn($category);
        $block->expects(static::once())
            ->method('setSetting')
            ->with(static::equalTo('categoryId'), static::equalTo(23));

        $blockService = $this->getMockForAbstractClass(AbstractCategoriesBlockService::class, [
            $this->twig, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ]);
        $blockService->preUpdate($block);
    }
}
