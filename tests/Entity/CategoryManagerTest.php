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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Entity\BaseCategory;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;

class CategoryManagerTest extends TestCase
{
    public function testGetRootCategoryWithChildren(): void
    {
        $context = $this->getMockForAbstractClass(ContextTest::class);
        $context->setId('1');
        $context->setName('default');
        $context->setEnabled(true);

        $categoryFoo = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($context);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

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

    public function testGetRootCategoriesForContext(): void
    {
        $context = $this->getMockForAbstractClass(ContextTest::class);
        $context->setId('1');
        $context->setName('default');
        $context->setEnabled(true);

        $categoryFoo = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($context);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

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

    public function testGetRootCategoriesSplitByContexts(): void
    {
        $contextFoo = $this->getMockForAbstractClass(ContextTest::class);
        $contextFoo->setId('1');
        $contextFoo->setName('foo');
        $contextFoo->setEnabled(true);

        $contextBar = $this->getMockForAbstractClass(ContextTest::class);
        $contextBar->setId('2');
        $contextBar->setName('bar');
        $contextBar->setEnabled(true);

        $categoryFoo = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($contextFoo);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        $categoryBar = $this->getMockForAbstractClass(CategoryTest::class);
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($contextBar);
        $categoryBar->setParent(null);
        $categoryBar->setEnabled(true);

        $categoryManager = $this->getCategoryManager(static function (MockObject $qb): void {
        }, [$categoryFoo, $categoryBar]);

        $categories = $categoryManager->getRootCategoriesSplitByContexts(false);

        $fooId = $contextFoo->getId();
        static::assertNotNull($fooId);
        static::assertArrayHasKey($fooId, $categories);
        $barId = $contextBar->getId();
        static::assertNotNull($barId);
        static::assertArrayHasKey($barId, $categories);
        static::assertContains($categoryFoo, $categories[$fooId]);
        static::assertContains($categoryBar, $categories[$barId]);
    }

    public function testGetBySlug(): void
    {
        $this
            ->getCategoryManager(static function (MockObject $qb): void {
                $qb->expects(static::exactly(3))->method('andWhere')->withConsecutive(
                    [static::equalTo('c.slug = :slug')],
                    [static::equalTo('c.context = :context')],
                    [static::equalTo('c.enabled = :enabled')]
                )->willReturn($qb);
                $qb->expects(static::exactly(3))->method('setParameter')->withConsecutive(
                    [static::equalTo('slug'), static::equalTo('theslug')],
                    [static::equalTo('context'), static::equalTo('contextA')],
                    [static::equalTo('enabled'), static::equalTo(false)]
                )->willReturn($qb);
            })
            ->getBySlug('theslug', 'contextA', false);
    }

    /**
     * @param object[] $createQueryResult
     */
    private function getCategoryManager(\Closure $qbCallback, array $createQueryResult = []): CategoryManager
    {
        $query = $this->createMock(AbstractQuery::class);
        $query->method('getResult')->willReturn($createQueryResult);

        $qb = $this->createMock(QueryBuilder::class);

        $qb->method('select')->willReturn($qb);
        $qb->method('getQuery')->willReturn($query);
        $qb->method('where')->willReturn($qb);
        $qb->method('orderBy')->willReturn($qb);
        $qb->method('setParameter')->willReturn($qb);

        $qbCallback($qb);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('createQueryBuilder')->willReturn($qb);

        $em = $this->createMock(EntityManager::class);
        $em->method('getRepository')->willReturn($repository);

        $registry = $this->getMockForAbstractClass(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($em);

        $contextManager = $this->createMock(ContextManagerInterface::class);

        return new CategoryManager(BaseCategory::class, $registry, $contextManager);
    }
}
