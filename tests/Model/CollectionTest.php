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
final class CollectionTest extends TestCase
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

        static::assertSame('Hello World', $collection->getName());
        static::assertSame('Hello World', $collection->__toString());
        static::assertSame('hello-world', $collection->getSlug());
        static::assertSame($time, $collection->getCreatedAt());
        static::assertSame($time, $collection->getUpdatedAt());
        static::assertTrue($collection->getEnabled());
        static::assertSame('My description', $collection->getDescription());
        static::assertSame($media, $collection->getMedia());
        static::assertSame($context, $collection->getContext());

        $collection->setName('');
        static::assertSame('n-a', $collection->getSlug());
        static::assertSame('n/a', $collection->__toString());

        $collection->setName('Привет мир');
        static::assertSame('privet-mir', $collection->getSlug());
        static::assertSame('Привет мир', $collection->__toString());

        $collection->setSlug('Custom Slug');
        static::assertSame('custom-slug', $collection->getSlug());
    }

    public function testPrePersist(): void
    {
        /** @var Collection $collection */
        $collection = $this->getMockForAbstractClass(Collection::class);
        $collection->prePersist();

        static::assertInstanceOf(\DateTime::class, $collection->getCreatedAt());
        static::assertInstanceOf(\DateTime::class, $collection->getUpdatedAt());
    }

    public function testPreUpdate(): void
    {
        /** @var Collection $collection */
        $collection = $this->getMockForAbstractClass(Collection::class);
        $collection->preUpdate();

        static::assertInstanceOf(\DateTime::class, $collection->getUpdatedAt());
    }
}
