<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Tests\Model;

use Sonata\ClassificationBundle\Model\Collection;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\MediaBundle\Model\MediaInterface;

/**
 * @author Dariusz Markowicz <dmarkowicz77@gmail.com>
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testSetterGetter()
    {
        $time = new \DateTime();

        /** @var ContextInterface $context */
        $context = $this->getMockBuilder('Sonata\ClassificationBundle\Model\ContextInterface')->getMock();

        /** @var MediaInterface $media */
        $media = $this->getMockBuilder('Sonata\MediaBundle\Model\MediaInterface')->getMock();

        /** @var Collection $collection */
        $collection = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Model\Collection');
        $collection->setName('Hello World');
        $collection->setCreatedAt($time);
        $collection->setUpdatedAt($time);
        $collection->setEnabled(true);
        $collection->setDescription('My description');
        $collection->setMedia($media);
        $collection->setContext($context);

        $this->assertEquals('Hello World', $collection->getName());
        $this->assertEquals('Hello World', $collection->__toString());
        $this->assertEquals('hello-world', $collection->getSlug());
        $this->assertEquals($time, $collection->getCreatedAt());
        $this->assertEquals($time, $collection->getUpdatedAt());
        $this->assertTrue($collection->getEnabled());
        $this->assertEquals('My description', $collection->getDescription());
        $this->assertEquals($media, $collection->getMedia());
        $this->assertEquals($context, $collection->getContext());

        $collection->setName('');
        $this->assertEquals('n-a', $collection->getSlug());
        $this->assertEquals('n/a', $collection->__toString());

        $collection->setName('Привет мир');
        $this->assertEquals('privet-mir', $collection->getSlug());
        $this->assertEquals('Привет мир', $collection->__toString());

        $collection->setSlug('Custom Slug');
        $this->assertEquals('custom-slug', $collection->getSlug());
    }

    public function testPrePersist()
    {
        /** @var Collection $collection */
        $collection = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Model\Collection');
        $collection->prePersist();

        $this->assertInstanceOf('\DateTime', $collection->getCreatedAt());
        $this->assertInstanceOf('\DateTime', $collection->getUpdatedAt());
    }

    public function testPreUpdate()
    {
        /** @var Collection $collection */
        $collection = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Model\Collection');
        $collection->preUpdate();

        $this->assertInstanceOf('\DateTime', $collection->getUpdatedAt());
    }
}
