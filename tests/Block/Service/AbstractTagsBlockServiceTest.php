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

use PHPUnit\Framework\MockObject\MockObject;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Test\BlockServiceTestCase;
use Sonata\ClassificationBundle\Admin\TagAdmin;
use Sonata\ClassificationBundle\Block\Service\AbstractTagsBlockService;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\ClassificationBundle\Tests\App\Entity\Tag;
use Twig\Environment;

/**
 * @author Christian Gripp <mail@core23.de>
 */
final class AbstractTagsBlockServiceTest extends BlockServiceTestCase
{
    /**
     * @var ContextManagerInterface&MockObject
     */
    private $contextManager;

    /**
     * @var TagManagerInterface&MockObject
     */
    private $tagManager;

    private TagAdmin $tagAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->twig = $this->createMock(Environment::class);
        $this->contextManager = $this->createMock(ContextManagerInterface::class);
        $this->tagManager = $this->createMock(TagManagerInterface::class);
        $this->tagAdmin = new TagAdmin('code', Tag::class, 'controller', $this->contextManager);
    }

    public function testDefaultSettings(): void
    {
        $blockService = $this->getMockForAbstractClass(AbstractTagsBlockService::class, [
            $this->twig, $this->contextManager, $this->tagManager, $this->tagAdmin,
        ]);
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings([
            'title' => null,
            'translation_domain' => null,
            'icon' => 'fa fa-tags',
            'class' => null,
            'tag' => false,
            'tagId' => null,
            'context' => null,
            'template' => '@SonataClassification/Block/base_block_tags.html.twig',
        ], $blockContext);
    }

    public function testLoad(): void
    {
        $tag = $this->getMockBuilder(TagInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $tag->expects(static::any())->method('getId')->willReturn(23);

        $this->tagManager->expects(static::any())
            ->method('find')
            ->with(static::equalTo('23'))
            ->willReturn($tag);

        $block = $this->createMock(BlockInterface::class);
        $block->expects(static::any())
            ->method('getSetting')
            ->with(static::equalTo('tagId'))
            ->willReturn(23);
        $block->expects(static::once())
            ->method('setSetting')
            ->with(static::equalTo('tagId'), static::equalTo($tag));

        $blockService = $this->getMockForAbstractClass(AbstractTagsBlockService::class, [
            $this->twig, $this->contextManager, $this->tagManager, $this->tagAdmin,
        ]);
        $blockService->load($block);
    }

    public function testPrePersist(): void
    {
        $tag = $this->getMockBuilder(TagInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $tag->expects(static::any())->method('getId')->willReturn(23);

        $block = $this->createMock(BlockInterface::class);
        $block->expects(static::any())
            ->method('getSetting')
            ->with(static::equalTo('tagId'))
            ->willReturn($tag);
        $block->expects(static::once())
            ->method('setSetting')
            ->with(static::equalTo('tagId'), static::equalTo(23));

        $blockService = $this->getMockForAbstractClass(AbstractTagsBlockService::class, [
            $this->twig, $this->contextManager, $this->tagManager, $this->tagAdmin,
        ]);
        $blockService->prePersist($block);
    }

    public function testPreUpdate(): void
    {
        $tag = $this->getMockBuilder(TagInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $tag->expects(static::any())->method('getId')->willReturn(23);

        $block = $this->createMock(BlockInterface::class);
        $block->expects(static::any())
            ->method('getSetting')
            ->with(static::equalTo('tagId'))
            ->willReturn($tag);
        $block->expects(static::once())
            ->method('setSetting')
            ->with(static::equalTo('tagId'), static::equalTo(23));

        $blockService = $this->getMockForAbstractClass(AbstractTagsBlockService::class, [
            $this->twig, $this->contextManager, $this->tagManager, $this->tagAdmin,
        ]);
        $blockService->preUpdate($block);
    }
}
