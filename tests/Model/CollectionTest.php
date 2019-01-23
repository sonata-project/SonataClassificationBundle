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
    public function testSetterGetter()
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

        $this->assertSame('Hello World', $collection->getName());
        $this->assertSame('Hello World', $collection->__toString());
        $this->assertSame('hello-world', $collection->getSlug());
        $this->assertSame($time, $collection->getCreatedAt());
        $this->assertSame($time, $collection->getUpdatedAt());
        $this->assertTrue($collection->getEnabled());
        $this->assertSame('My description', $collection->getDescription());
        $this->assertSame($media, $collection->getMedia());
        $this->assertSame($context, $collection->getContext());

        $collection->setName('');
        $this->assertSame('n-a', $collection->getSlug());
        $this->assertSame('n/a', $collection->__toString());

        $collection->setName('Привет мир');
        $this->assertSame('privet-mir', $collection->getSlug());
        $this->assertSame('Привет мир', $collection->__toString());

        $collection->setSlug('Custom Slug');
        $this->assertSame('custom-slug', $collection->getSlug());
    }

    public function testPrePersist()
    {
        /** @var Collection $collection */
        $collection = $this->getMockForAbstractClass(Collection::class);
        $collection->prePersist();

        $this->assertInstanceOf(\DateTime::class, $collection->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $collection->getUpdatedAt());
    }

    public function testPreUpdate()
    {
        /** @var Collection $collection */
        $collection = $this->getMockForAbstractClass(Collection::class);
        $collection->preUpdate();

        $this->assertInstanceOf(\DateTime::class, $collection->getUpdatedAt());
    }
}
