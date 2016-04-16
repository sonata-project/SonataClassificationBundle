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

class AbstractCategoriesBlockServiceTest extends AbstractBlockServiceTest
{
    /**
     * @var FakeTemplating
     */
    protected $templating;

    /**
     * @var ContextManagerInterface
     */
    protected $contextManager;

    /**
     * @var CategoryManagerInterface
     */
    protected $categoryManager;

    /**
     * @var CategoryAdmin
     */
    protected $categoryAdmin;

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
}
