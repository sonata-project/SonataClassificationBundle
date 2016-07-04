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

use Sonata\BlockBundle\Tests\Block\AbstractBlockServiceTest;
use Sonata\BlockBundle\Tests\Block\Service\FakeTemplating;
use Sonata\ClassificationBundle\Admin\CategoryAdmin;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;

/**
 * @author Christian Gripp <mail@core23.de>
 */
final class AbstractCategoriesBlockServiceTest extends AbstractBlockServiceTest
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
        $this->contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $this->categoryManager = $this->getMock('Sonata\ClassificationBundle\Model\CategoryManagerInterface');
        $this->categoryAdmin = $this->getMockBuilder('Sonata\ClassificationBundle\Admin\CategoryAdmin')->disableOriginalConstructor()->getMock();
    }

    public function testDefaultSettings()
    {
        $blockService = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Block\Service\AbstractCategoriesBlockService', array(
            'block.service', $this->templating, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ));
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings(array(
            'title' => 'Categories',
            'category' => false,
            'categoryId' => null,
            'context' => 'default',
            'template' => 'SonataClassificationBundle:Block:base_block_categories.html.twig',
        ), $blockContext);
    }

    public function testLoad()
    {
        $category = $this->getMockBuilder('Sonata\ClassificationBundle\Model\CategoryInterfacer')
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMock();
        $category->expects($this->any())->method('getId')->will($this->returnValue(23));

        $this->categoryManager->expects($this->any())
            ->method('find')
            ->with($this->equalTo('23'))
            ->will($this->returnValue($category));

        $block = $this->getMock('Sonata\BlockBundle\Model\BlockInterface');
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('categoryId'))
            ->will($this->returnValue(23));
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('categoryId'), $this->equalTo($category));

        $blockService = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Block\Service\AbstractCategoriesBlockService', array(
            'block.service', $this->templating, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ));
        $blockService->load($block);
    }

    public function testPrePersist()
    {
        $category = $this->getMockBuilder('Sonata\ClassificationBundle\Model\CategoryInterfacer')
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMock();
        $category->expects($this->any())->method('getId')->will($this->returnValue(23));

        $block = $this->getMock('Sonata\BlockBundle\Model\BlockInterface');
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('categoryId'))
            ->will($this->returnValue($category));
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('categoryId'), $this->equalTo(23));

        $blockService = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Block\Service\AbstractCategoriesBlockService', array(
            'block.service', $this->templating, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ));
        $blockService->prePersist($block);
    }

    public function testPreUpdate()
    {
        $category = $this->getMockBuilder('Sonata\ClassificationBundle\Model\CategoryInterfacer')
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMock();
        $category->expects($this->any())->method('getId')->will($this->returnValue(23));

        $block = $this->getMock('Sonata\BlockBundle\Model\BlockInterface');
        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('categoryId'))
            ->will($this->returnValue($category));
        $block->expects($this->once())
            ->method('setSetting')
            ->with($this->equalTo('categoryId'), $this->equalTo(23));

        $blockService = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Block\Service\AbstractCategoriesBlockService', array(
            'block.service', $this->templating, $this->contextManager, $this->categoryManager, $this->categoryAdmin,
        ));
        $blockService->preUpdate($block);
    }
}
