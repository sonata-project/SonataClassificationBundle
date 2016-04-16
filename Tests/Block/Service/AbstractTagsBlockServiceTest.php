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

class AbstractTagsBlockServiceTest extends AbstractBlockServiceTest
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
     * @var TagManagerInterface
     */
    protected $tagManager;

    /**
     * @var TagAdmin
     */
    protected $tagAdmin;

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
}
