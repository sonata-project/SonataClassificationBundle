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
use Sonata\ClassificationBundle\Entity\BaseCollection;
use Sonata\ClassificationBundle\Entity\CollectionManager;

final class CollectionManagerTest extends TestCase
{
    public function testGetBySlug(): void
    {
        $this
            ->getCollectionManager(static function (MockObject $qb): void {
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

    public function testGetByContext(): void
    {
        $this
            ->getCollectionManager(static function (MockObject $qb): void {
                $qb->expects(static::exactly(2))->method('andWhere')->withConsecutive(
                    [static::equalTo('c.context = :context')],
                    [static::equalTo('c.enabled = :enabled')]
                )->willReturn($qb);
                $qb->expects(static::exactly(2))->method('setParameter')->withConsecutive(
                    [static::equalTo('context'), static::equalTo('contextA')],
                    [static::equalTo('enabled'), static::equalTo(false)]
                )->willReturn($qb);
            })
            ->getByContext('contextA', false);
    }

    /**
     * @param object[] $createQueryResult
     */
    private function getCollectionManager(\Closure $qbCallback, array $createQueryResult = []): CollectionManager
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

        return new CollectionManager(BaseCollection::class, $registry);
    }
}
