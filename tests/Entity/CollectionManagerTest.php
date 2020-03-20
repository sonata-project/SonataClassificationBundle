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
use Sonata\ClassificationBundle\Entity\BaseCollection;
use Sonata\ClassificationBundle\Entity\CollectionManager;
use Sonata\Doctrine\Test\EntityManagerMockFactoryTrait;

class CollectionManagerTest extends TestCase
{
    use EntityManagerMockFactoryTrait;

    public function testGetPager(): void
    {
        $self = $this;
        $this
            ->getCollectionManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue([]));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with([]);
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithEnabledCollections(): void
    {
        $self = $this;
        $this
            ->getCollectionManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue([]));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('c.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameters')->with(['enabled' => true]);
            })
            ->getPager([
                'enabled' => true,
            ], 1);
    }

    public function testGetPagerWithDisabledCollections(): void
    {
        $self = $this;
        $this
            ->getCollectionManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue([]));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('c.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameters')->with(['enabled' => false]);
            })
            ->getPager([
                'enabled' => false,
            ], 1);
    }

    public function testGetBySlug(): void
    {
        $self = $this;
        $this
            ->getCollectionManager(static function ($qb) use ($self): void {
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

    public function testGetByContext(): void
    {
        $self = $this;
        $this
            ->getCollectionManager(static function ($qb) use ($self): void {
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    [$self->equalTo('c.context = :context')],
                    [$self->equalTo('c.enabled = :enabled')]
                )->willReturn($qb);
                $qb->expects($self->exactly(2))->method('setParameter')->withConsecutive(
                    [$self->equalTo('context'), $self->equalTo('contextA')],
                    [$self->equalTo('enabled'), $self->equalTo(false)]
                )->willReturn($qb);
            }, [])
            ->getByContext('contextA', false);
    }

    protected function getCollectionManager($qbCallback, $createQueryResult = null)
    {
        $em = $this->createEntityManagerMock($qbCallback, []);

        if (null !== $createQueryResult) {
            $query = $this->createMock(AbstractQuery::class);
            $query->expects($this->once())->method('execute')->willReturn($createQueryResult);
            $query->expects($this->any())->method('setParameter')->willReturn($query);
            $em->expects($this->once())->method('createQuery')->willReturn($query);
        }

        $registry = $this->getMockForAbstractClass(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->willReturn($em);

        return new CollectionManager(BaseCollection::class, $registry);
    }
}
