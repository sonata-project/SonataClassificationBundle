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
class TagTest extends TestCase
{
    public function testSetterGetter()
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

        $this->assertSame('Hello World', $tag->getName());
        $this->assertSame('Hello World', $tag->__toString());
        $this->assertSame('hello-world', $tag->getSlug());
        $this->assertSame($time, $tag->getCreatedAt());
        $this->assertSame($time, $tag->getUpdatedAt());
        $this->assertTrue($tag->getEnabled());
        $this->assertSame($context, $tag->getContext());

        $tag->setName('');
        $this->assertSame('n-a', $tag->getSlug());
        $this->assertSame('n/a', $tag->__toString());

        $tag->setName('Привет мир');
        $this->assertSame('privet-mir', $tag->getSlug());
        $this->assertSame('Привет мир', $tag->__toString());

        $tag->setSlug('Custom Slug');
        $this->assertSame('custom-slug', $tag->getSlug());
    }

    public function testPreUpdate()
    {
        /** @var Tag $tag */
        $tag = $this->getMockForAbstractClass(Tag::class);
        $tag->preUpdate();

        $this->assertInstanceOf(\DateTime::class, $tag->getUpdatedAt());
    }
}
