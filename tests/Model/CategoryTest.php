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
use Sonata\MediaBundle\Model\MediaInterface;

/**
 * @author Dariusz Markowicz <dmarkowicz77@gmail.com>
 */
class CategoryTest extends TestCase
{
    public function testSetterGetter(): void
    {
        $time = new \DateTime();

        /** @var ContextInterface $context */
        $context = $this->createMock(ContextInterface::class);

        /** @var MediaInterface $media */
        $media = $this->createMock(MediaInterface::class);

        /** @var Category $category */
        $category = $this->getMockForAbstractClass(Category::class);
        $category->setName('Hello World');
        $category->setEnabled(true);
        $category->setDescription('My description');
        $category->setCreatedAt($time);
        $category->setUpdatedAt($time);
        $category->setPosition(2);
        $category->setMedia($media);
        $category->setContext($context);

        $this->assertEquals('Hello World', $category->getName());
        $this->assertEquals('Hello World', $category->__toString());
        $this->assertEquals('hello-world', $category->getSlug());
        $this->assertTrue($category->getEnabled());
        $this->assertEquals('My description', $category->getDescription());
        $this->assertEquals($time, $category->getCreatedAt());
        $this->assertEquals($time, $category->getUpdatedAt());
        $this->assertEquals(2, $category->getPosition());
        $this->assertEquals($media, $category->getMedia());
        $this->assertEquals($context, $category->getContext());

        $category->setName('');
        $this->assertEquals('n-a', $category->getSlug());
        $this->assertEquals('n/a', $category->__toString());

        $category->setName('Привет мир');
        $this->assertEquals('privet-mir', $category->getSlug());
        $this->assertEquals('Привет мир', $category->__toString());

        $category->setSlug('Custom Slug');
        $this->assertEquals('custom-slug', $category->getSlug());
    }

    public function testParent(): void
    {
        /** @var Category $parent */
        $parent = $this->getMockForAbstractClass(Category::class);

        /** @var Category $category */
        $category = $this->getMockForAbstractClass(Category::class);
        $category->setParent($parent);
        $this->assertEquals($parent, $category->getParent());
        $this->assertCount(1, $parent->getChildren());
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
        $this->assertFalse($category->hasChildren());

        $category->addChild($cat1);
        $category->addChild($cat2);
        $category->addChild($cat3);
        $this->assertEquals($context, $cat1->getContext()); // child context set to parent
        $this->assertEquals($category, $cat1->getParent());
        $this->assertTrue($category->hasChildren());
        $this->assertCount(3, $category->getChildren());

        // Category::removeChild implementation use getId() which is not a part of interface nor model, skipping

        // No type hint in interface so assume basic array.
        $category->setChildren([]);
        $this->assertCount(0, $category->getChildren());
        $category->setChildren([$cat1, $cat2, $cat3]);
        $this->assertCount(3, $category->getChildren());
    }

    public function testPrePersist(): void
    {
        /** @var Category $category */
        $category = $this->getMockForAbstractClass(Category::class);
        $category->prePersist();

        $this->assertInstanceOf(\DateTime::class, $category->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $category->getUpdatedAt());
    }

    public function testPreUpdate(): void
    {
        /** @var Category $category */
        $category = $this->getMockForAbstractClass(Category::class);
        $category->preUpdate();

        $this->assertInstanceOf(\DateTime::class, $category->getUpdatedAt());
    }
}
