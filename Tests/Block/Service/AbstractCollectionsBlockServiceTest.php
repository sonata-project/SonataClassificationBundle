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
use Sonata\ClassificationBundle\Admin\CollectionAdmin;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;

class AbstractCollectionsBlockServiceTest extends AbstractBlockServiceTest
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
     * @var CollectionManagerInterface
     */
    protected $collectionManager;

    /**
     * @var CollectionAdmin
     */
    protected $collectionAdmin;

    protected function setUp()
    {
        parent::setUp();

        $this->templating = new FakeTemplating();
        $this->contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $this->collectionManager = $this->getMock('Sonata\ClassificationBundle\Model\CollectionManagerInterface');
        $this->collectionAdmin = $this->getMockBuilder('Sonata\ClassificationBundle\Admin\CollectionAdmin')->disableOriginalConstructor()->getMock();
    }

    public function testDefaultSettings()
    {
        $blockService = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Block\Service\AbstractCollectionsBlockService', array(
            'block.service', $this->templating, $this->contextManager, $this->collectionManager, $this->collectionAdmin,
        ));
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings(array(
            'title' => 'Collections',
            'collection' => false,
            'collectionId' => null,
            'context' => null,
            'template' => 'SonataClassificationBundle:Block:base_block_collections.html.twig',
        ), $blockContext);
    }
}
