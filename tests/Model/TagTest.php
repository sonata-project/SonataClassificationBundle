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
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\Tag;

/**
 * @author Dariusz Markowicz <dmarkowicz77@gmail.com>
 */
final class TagTest extends TestCase
{
    public function testSetterGetter(): void
    {
        $time = new \DateTime();

        /** @var ContextInterface $context */
        $context = $this->createMock(ContextInterface::class);

        /** @var Tag $tag */
        $tag = $this->getMockForAbstractClass(Tag::class);
        $tag->setName('Hello World');
        $tag->setCreatedAt($time);
        $tag->setUpdatedAt($time);
        $tag->setEnabled(true);
        $tag->setContext($context);

        static::assertSame('Hello World', $tag->getName());
        static::assertSame('Hello World', $tag->__toString());
        static::assertSame('hello-world', $tag->getSlug());
        static::assertSame($time, $tag->getCreatedAt());
        static::assertSame($time, $tag->getUpdatedAt());
        static::assertTrue($tag->getEnabled());
        static::assertSame($context, $tag->getContext());

        $tag->setName('');
        static::assertSame('n-a', $tag->getSlug());
        static::assertSame('n/a', $tag->__toString());

        $tag->setName('Привет мир');
        static::assertSame('privet-mir', $tag->getSlug());
        static::assertSame('Привет мир', $tag->__toString());

        $tag->setSlug('Custom Slug');
        static::assertSame('custom-slug', $tag->getSlug());
    }

    public function testPreUpdate(): void
    {
        /** @var Tag $tag */
        $tag = $this->getMockForAbstractClass(Tag::class);
        $tag->preUpdate();

        static::assertInstanceOf(\DateTime::class, $tag->getUpdatedAt());
    }
}
