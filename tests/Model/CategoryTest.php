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
use Sonata\ClassificationBundle\Model\Category;
use Sonata\ClassificationBundle\Model\ContextInterface;

/**
 * @author Dariusz Markowicz <dmarkowicz77@gmail.com>
 */
final class CategoryTest extends TestCase
{
    public function testSetterGetter(): void
    {
        $time = new \DateTime();

        /** @var ContextInterface $context */
        $context = $this->createMock(ContextInterface::class);

        /** @var Category $category */
        $category = $this->getMockForAbstractClass(Category::class);
        $category->setName('Hello World');
        $category->setEnabled(true);
        $category->setDescription('My description');
        $category->setCreatedAt($time);
        $category->setUpdatedAt($time);
        $category->setPosition(2);
        $category->setContext($context);

        static::assertSame('Hello World', $category->getName());
        static::assertSame('Hello World', $category->__toString());
        static::assertSame('hello-world', $category->getSlug());
        static::assertTrue($category->getEnabled());
        static::assertSame('My description', $category->getDescription());
        static::assertSame($time, $category->getCreatedAt());
        static::assertSame($time, $category->getUpdatedAt());
        static::assertSame(2, $category->getPosition());
        static::assertSame($context, $category->getContext());

        $category->setName(null);
        static::assertSame('n-a', $category->getSlug());
        static::assertSame('n/a', $category->__toString());

        $category->setName('Привет мир');
        static::assertSame('privet-mir', $category->getSlug());
        static::assertSame('Привет мир', $category->__toString());

        $category->setSlug('Custom Slug');
        static::assertSame('custom-slug', $category->getSlug());
    }

    public function testParent(): void
    {
        /** @var Category $parent */
        $parent = $this->getMockForAbstractClass(Category::class);

        /** @var Category $category */
        $category = $this->getMockForAbstractClass(Category::class);
        $category->setParent($parent);
        static::assertSame($parent, $category->getParent());
        static::assertCount(1, $parent->getChildren());
    }

    public function testChildren(): void
    {
        /** @var Category $cat1 */
        $cat1 = $this->getMockForAbstractClass(Category::class);
        /** @var Category $cat2 */
        $cat2 = $this->getMockForAbstractClass(Category::class);
        /** @var Category $cat3 */
        $cat3 = $this->getMockForAbstractClass(Category::class);

        /** @var ContextInterface $context */
        $context = $this->createMock(ContextInterface::class);

        /** @var Category $category */
        $category = $this->getMockForAbstractClass(Category::class);
        $category->setContext($context);
        static::assertFalse($category->hasChildren());

        $category->addChild($cat1);
        $category->addChild($cat2);
        $category->addChild($cat3);
        static::assertSame($context, $cat1->getContext()); // child context set to parent
        static::assertSame($category, $cat1->getParent());
        static::assertTrue($category->hasChildren());
        static::assertCount(3, $category->getChildren());

        // Category::removeChild implementation use getId() which is not a part of interface nor model, skipping

        // No type hint in interface so assume basic array.
        $category->setChildren([]);
        static::assertCount(0, $category->getChildren());
        $category->setChildren([$cat1, $cat2, $cat3]);
        static::assertCount(3, $category->getChildren());
    }

    public function testPrePersist(): void
    {
        /** @var Category $category */
        $category = $this->getMockForAbstractClass(Category::class);
        $category->prePersist();

        static::assertInstanceOf(\DateTime::class, $category->getCreatedAt());
        static::assertInstanceOf(\DateTime::class, $category->getUpdatedAt());
    }

    public function testPreUpdate(): void
    {
        /** @var Category $category */
        $category = $this->getMockForAbstractClass(Category::class);
        $category->preUpdate();

        static::assertInstanceOf(\DateTime::class, $category->getUpdatedAt());
    }
}
