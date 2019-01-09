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

namespace Sonata\ClassificationBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Model\Collection;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\MediaBundle\Model\MediaInterface;

/**
 * @author Dariusz Markowicz <dmarkowicz77@gmail.com>
 */
class CollectionTest extends TestCase
{
    public function testSetterGetter(): void
    {
        $time = new \DateTime();

        /** @var ContextInterface $context */
        $context = $this->createMock(ContextInterface::class);

        /** @var MediaInterface $media */
        $media = $this->createMock(MediaInterface::class);

        /** @var Collection $collection */
        $collection = $this->getMockForAbstractClass(Collection::class);
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

    public function testPrePersist(): void
    {
        /** @var Collection $collection */
        $collection = $this->getMockForAbstractClass(Collection::class);
        $collection->prePersist();

        $this->assertInstanceOf(\DateTime::class, $collection->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $collection->getUpdatedAt());
    }

    public function testPreUpdate(): void
    {
        /** @var Collection $collection */
        $collection = $this->getMockForAbstractClass(Collection::class);
        $collection->preUpdate();

        $this->assertInstanceOf(\DateTime::class, $collection->getUpdatedAt());
    }
}
