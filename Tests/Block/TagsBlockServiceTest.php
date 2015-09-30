<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Tests\Block;

use Sonata\BlockBundle\Tests\Block\AbstractBlockServiceTest;
use Sonata\ClassificationBundle\Block\TagsBlockService;

/**
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class TagsBlockServiceTest extends AbstractBlockServiceTest
{
    public function testDefaultSettings()
    {
        $manager = $this->getMockBuilder('Sonata\ClassificationBundle\Model\TagManagerInterface')->disableOriginalConstructor()->getMock();
        $pool = $this->getMockBuilder('Sonata\AdminBundle\Admin\Pool')->disableOriginalConstructor()->getMock();
        $blockService = new TagsBlockService('foo', $this->templating, $manager, $pool);
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings(array(
            'context'    => 'default',
            'limit'      => null,
            'title'      => 'Tags',
            'template'   => 'SonataClassificationBundle:Block:tags.html.twig',
        ), $blockContext);
    }
}
