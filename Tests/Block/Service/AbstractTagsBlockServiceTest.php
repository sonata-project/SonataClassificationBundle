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

use Sonata\AdminBundle\Tests\Fixtures\Admin\TagAdmin;
use Sonata\BlockBundle\Tests\Block\AbstractBlockServiceTest;
use Sonata\BlockBundle\Tests\Block\Service\FakeTemplating;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;

/**
 * @author Christian Gripp <mail@core23.de>
 */
final class AbstractTagsBlockServiceTest extends AbstractBlockServiceTest
{
    /**
     * @var ContextManagerInterface
     */
    private $contextManager;

    /**
     * @var TagManagerInterface
     */
    private $tagManager;

    /**
     * @var TagAdmin
     */
    private $tagAdmin;

    protected function setUp()
    {
        parent::setUp();

        $this->templating = new FakeTemplating();
        $this->contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $this->tagManager = $this->getMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $this->tagAdmin = $this->getMockBuilder('Sonata\ClassificationBundle\Admin\TagAdmin')->disableOriginalConstructor()->getMock();
    }

    public function testDefaultSettings()
    {
        $blockService = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Block\Service\AbstractTagsBlockService', array(
            'block.service', $this->templating, $this->contextManager, $this->tagManager, $this->tagAdmin,
        ));
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings(array(
            'title' => 'Tags',
            'tag' => false,
            'tagId' => null,
            'context' => null,
            'template' => 'SonataClassificationBundle:Block:base_block_tags.html.twig',
        ), $blockContext);
    }

    public function testLoad()
    {
        $tag = $this->getMockBuilder('Sonata\ClassificationBundle\Model\TagInterfacer')
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMock();
        $tag->expects($this->any())->method('getId')->will($this->returnValue(23));

        $this->tagManager->expects($this->any())
            ->method('find')
            ->with($this->equalTo('23'))
            ->will($this->returnValue($tag));

        $block = $this->getMock('Sonata\BlockBundle\Model\BlockInterface');
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('tagId'))
            ->will($this->returnValue(23));
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('tagId'), $this->equalTo($tag));

        $blockService = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Block\Service\AbstractTagsBlockService', array(
            'block.service', $this->templating, $this->contextManager, $this->tagManager, $this->tagAdmin,
        ));
        $blockService->load($block);
    }

    public function testPrePersist()
    {
        $tag = $this->getMockBuilder('Sonata\ClassificationBundle\Model\TagInterfacer')
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMock();
        $tag->expects($this->any())->method('getId')->will($this->returnValue(23));

        $block = $this->getMock('Sonata\BlockBundle\Model\BlockInterface');
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('tagId'))
            ->will($this->returnValue($tag));
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('tagId'), $this->equalTo(23));

        $blockService = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Block\Service\AbstractTagsBlockService', array(
            'block.service', $this->templating, $this->contextManager, $this->tagManager, $this->tagAdmin,
        ));
        $blockService->prePersist($block);
    }

    public function testPreUpdate()
    {
        $tag = $this->getMockBuilder('Sonata\ClassificationBundle\Model\TagInterfacer')
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMock();
        $tag->expects($this->any())->method('getId')->will($this->returnValue(23));

        $block = $this->getMock('Sonata\BlockBundle\Model\BlockInterface');
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('tagId'))
            ->will($this->returnValue($tag));
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('tagId'), $this->equalTo(23));

        $blockService = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Block\Service\AbstractTagsBlockService', array(
            'block.service', $this->templating, $this->contextManager, $this->tagManager, $this->tagAdmin,
        ));
        $blockService->preUpdate($block);
    }
}
