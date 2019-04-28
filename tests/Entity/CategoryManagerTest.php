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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Entity\BaseCategory;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\Doctrine\Test\EntityManagerMockFactory;

class CategoryManagerTest extends TestCase
{
    public function testGetPager(): void
    {
        $self = $this;
        $this
            ->getCategoryManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue([]));
                $qb->expects($self->exactly(1))->method('andWhere')->withConsecutive(
                    [$self->equalTo('c.context = :context')]
                );
                $qb->expects($self->once())->method('setParameters')->with(['context' => 'default']);
            })
            ->getPager(['context' => 'default'], 1);
    }

    public function testGetPagerWithEnabledCategories(): void
    {
        $self = $this;
        $this
            ->getCategoryManager(static function ($qb) use ($self): void {
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

    public function testGetPagerWithDisabledCategories(): void
    {
        $self = $this;
        $this
            ->getCategoryManager(static function ($qb) use ($self): void {
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

        $categoryManager = $this->getCategoryManager(static function ($qb): void {
        }, $categories);

        $this->assertSame($categoryManager->getCategories($context), $categories);
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

        $categoryManager = $this->getCategoryManager(static function ($qb): void {
        }, [$categoryFoo, $categoryBar]);

        $categoryFoo = $categoryManager->getRootCategoryWithChildren($categoryFoo);
        $this->assertContains($categoryBar, $categoryFoo->getChildren());
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

        $categoryManager = $this->getCategoryManager(static function ($qb): void {
        }, [$categoryFoo]);

        $categoryBar = $categoryManager->getRootCategory($context);
        $this->assertSame($categoryFoo, $categoryBar);
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

        $categoryManager = $this->getCategoryManager(static function ($qb): void {
        }, [$categoryFoo, $categoryBar]);

        $categories = $categoryManager->getRootCategoriesForContext($context);
        $this->assertCount(1, $categories);
        $this->assertContains($categoryFoo, $categories);
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

        $categoryManager = $this->getCategoryManager(static function ($qb): void {
        }, [$categoryFoo, $categoryBar]);

        $categories = $categoryManager->getRootCategories(false);
        $this->assertArrayHasKey($contextFoo->getId(), $categories);
        $this->assertArrayHasKey($contextBar->getId(), $categories);
        $this->assertSame($categoryFoo, $categories[$contextFoo->getId()]);
        $this->assertSame($categoryBar, $categories[$contextBar->getId()]);
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

        $categoryManager = $this->getCategoryManager(static function ($qb): void {
        }, [$categoryFoo, $categoryBar]);

        $categories = $categoryManager->getRootCategoriesSplitByContexts(false);
        $this->assertArrayHasKey($contextFoo->getId(), $categories);
        $this->assertArrayHasKey($contextBar->getId(), $categories);
        $this->assertContains($categoryFoo, $categories[$contextFoo->getId()]);
        $this->assertContains($categoryBar, $categories[$contextBar->getId()]);
    }

    protected function getCategoryManager($qbCallback, $createQueryResult = null)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, []);

        if (null !== $createQueryResult) {
            $query = $this->createMock(AbstractQuery::class);
            $query->expects($this->once())->method('execute')->will($this->returnValue($createQueryResult));
            $query->expects($this->any())->method('setParameter')->will($this->returnValue($query));
            $em->expects($this->once())->method('createQuery')->will($this->returnValue($query));
        }

        $registry = $this->getMockForAbstractClass(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $contextManager = $this->createMock(ContextManagerInterface::class);

        return new CategoryManager(BaseCategory::class, $registry, $contextManager);
    }
}
