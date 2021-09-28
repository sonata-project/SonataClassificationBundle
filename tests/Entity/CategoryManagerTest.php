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

namespace Sonata\ClassificationBundle\Tests\Entity;

use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Entity\BaseCategory;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\Doctrine\Test\EntityManagerMockFactoryTrait;

class CategoryManagerTest extends TestCase
{
    use EntityManagerMockFactoryTrait;

    /**
     * NEXT_MAJOR: Remove this class.
     *
     * @group legacy
     */
    public function testGetPager(): void
    {
        $self = $this;
        $this
            ->getCategoryManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue([]));
                $qb->expects($self->exactly(1))->method('andWhere')->withConsecutive(
                    [$self->equalTo('c.context = :context')]
                );
                $qb->expects($self->once())->method('setParameters')->with(['context' => 'default']);
            })
            ->getPager(['context' => 'default'], 1);
    }

    /**
     * NEXT_MAJOR: Remove this class.
     *
     * @group legacy
     */
    public function testGetPagerWithEnabledCategories(): void
    {
        $self = $this;
        $this
            ->getCategoryManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue([]));
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    [$self->equalTo('c.context = :context')],
                    [$self->equalTo('c.enabled = :enabled')]
                );
                $qb->expects($self->once())->method('setParameters')->with(['enabled' => true, 'context' => 'default']);
            })
            ->getPager([
                'enabled' => true,
                'context' => 'default',
            ], 1);
    }

    /**
     * NEXT_MAJOR: Remove this class.
     *
     * @group legacy
     */
    public function testGetPagerWithDisabledCategories(): void
    {
        $self = $this;
        $this
            ->getCategoryManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue([]));
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    [$self->equalTo('c.context = :context')],
                    [$self->equalTo('c.enabled = :enabled')]
                );
                $qb->expects($self->once())->method('setParameters')->with(['enabled' => false, 'context' => 'default']);
            })
            ->getPager([
                'enabled' => false,
                'context' => 'default',
            ], 1);
    }

    public function testGetCategoriesWithMultipleRootsInContext(): void
    {
        /** @var ContextTest $context */
        $context = $this->getMockForAbstractClass(ContextTest::class);
        $context->setId(1);
        $context->setName('default');
        $context->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($context);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        /** @var CategoryTest $categoryBar */
        $categoryBar = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($context);
        $categoryBar->setParent(null);
        $categoryBar->setEnabled(true);

        $categories = [$categoryFoo, $categoryBar];

        $categoryManager = $this->getCategoryManager(static function (MockObject $qb): void {
        }, $categories);

        static::assertSame($categoryManager->getCategories($context), $categories);
    }

    public function testGetRootCategoryWithChildren(): void
    {
        /** @var ContextTest $context */
        $context = $this->getMockForAbstractClass(ContextTest::class);
        $context->setId(1);
        $context->setName('default');
        $context->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($context);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        /** @var CategoryTest $categoryBar */
        $categoryBar = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($context);
        $categoryBar->setParent($categoryFoo);
        $categoryBar->setEnabled(true);

        $categoryManager = $this->getCategoryManager(static function (MockObject $qb): void {
        }, [$categoryFoo, $categoryBar]);

        $categoryFoo = $categoryManager->getRootCategoryWithChildren($categoryFoo);
        static::assertContains($categoryBar, $categoryFoo->getChildren());
    }

    public function testGetRootCategory(): void
    {
        /** @var ContextTest $context */
        $context = $this->getMockForAbstractClass(ContextTest::class);
        $context->setId(1);
        $context->setName('default');
        $context->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($context);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        $categoryManager = $this->getCategoryManager(static function (MockObject $qb): void {
        }, [$categoryFoo]);

        $categoryBar = $categoryManager->getRootCategory($context);
        static::assertSame($categoryFoo, $categoryBar);
    }

    public function testGetRootCategoriesForContext(): void
    {
        /** @var ContextTest $context */
        $context = $this->getMockForAbstractClass(ContextTest::class);
        $context->setId(1);
        $context->setName('default');
        $context->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($context);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        /** @var CategoryTest $categoryBar */
        $categoryBar = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($context);
        $categoryBar->setParent($categoryFoo);
        $categoryBar->setEnabled(true);

        $categoryManager = $this->getCategoryManager(static function (MockObject $qb): void {
        }, [$categoryFoo, $categoryBar]);

        $categories = $categoryManager->getRootCategoriesForContext($context);
        static::assertCount(1, $categories);
        static::assertContains($categoryFoo, $categories);
    }

    public function testGetRootCategories(): void
    {
        /** @var ContextTest $contextFoo */
        $contextFoo = $this->getMockForAbstractClass(ContextTest::class);
        $contextFoo->setId(1);
        $contextFoo->setName('foo');
        $contextFoo->setEnabled(true);

        /** @var ContextTest $contextBar */
        $contextBar = $this->getMockForAbstractClass(ContextTest::class);
        $contextBar->setId(2);
        $contextBar->setName('bar');
        $contextBar->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($contextFoo);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        /** @var CategoryTest $categoryBar */
        $categoryBar = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($contextBar);
        $categoryBar->setParent(null);
        $categoryBar->setEnabled(true);

        $categoryManager = $this->getCategoryManager(static function (MockObject $qb): void {
        }, [$categoryFoo, $categoryBar]);

        $categories = $categoryManager->getRootCategories(false);
        static::assertArrayHasKey($contextFoo->getId(), $categories);
        static::assertArrayHasKey($contextBar->getId(), $categories);
        static::assertSame($categoryFoo, $categories[$contextFoo->getId()]);
        static::assertSame($categoryBar, $categories[$contextBar->getId()]);
    }

    public function testGetRootCategoriesSplitByContexts(): void
    {
        /** @var ContextTest $contextFoo */
        $contextFoo = $this->getMockForAbstractClass(ContextTest::class);
        $contextFoo->setId(1);
        $contextFoo->setName('foo');
        $contextFoo->setEnabled(true);

        /** @var ContextTest $contextBar */
        $contextBar = $this->getMockForAbstractClass(ContextTest::class);
        $contextBar->setId(2);
        $contextBar->setName('bar');
        $contextBar->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($contextFoo);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        /** @var CategoryTest $categoryBar */
        $categoryBar = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($contextBar);
        $categoryBar->setParent(null);
        $categoryBar->setEnabled(true);

        $categoryManager = $this->getCategoryManager(static function (MockObject $qb): void {
        }, [$categoryFoo, $categoryBar]);

        $categories = $categoryManager->getRootCategoriesSplitByContexts(false);
        static::assertArrayHasKey($contextFoo->getId(), $categories);
        static::assertArrayHasKey($contextBar->getId(), $categories);
        static::assertContains($categoryFoo, $categories[$contextFoo->getId()]);
        static::assertContains($categoryBar, $categories[$contextBar->getId()]);
    }

    public function testGetBySlug(): void
    {
        $self = $this;
        $this
            ->getCategoryManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->exactly(3))->method('andWhere')->withConsecutive(
                    [$self->equalTo('c.slug = :slug')],
                    [$self->equalTo('c.context = :context')],
                    [$self->equalTo('c.enabled = :enabled')]
                )->willReturn($qb);
                $qb->expects($self->exactly(3))->method('setParameter')->withConsecutive(
                    [$self->equalTo('slug'), $self->equalTo('theslug')],
                    [$self->equalTo('context'), $self->equalTo('contextA')],
                    [$self->equalTo('enabled'), $self->equalTo(false)]
                )->willReturn($qb);
            })
            ->getBySlug('theslug', 'contextA', false);
    }

    private function getCategoryManager($qbCallback, $createQueryResult = null): CategoryManager
    {
        $em = $this->createEntityManagerMock($qbCallback, []);

        if (null !== $createQueryResult) {
            $query = $this->createMock(AbstractQuery::class);
            $query->expects(static::once())->method('execute')->willReturn($createQueryResult);
            $query->method('setParameter')->willReturn($query);
            $em->expects(static::once())->method('createQuery')->willReturn($query);
        }

        $registry = $this->getMockForAbstractClass(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($em);

        $contextManager = $this->createMock(ContextManagerInterface::class);

        return new CategoryManager(BaseCategory::class, $registry, $contextManager);
    }
}
