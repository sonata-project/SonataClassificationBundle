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
use PHPUnit\Framework\MockObject\MockObject;
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
            ->getCollectionManager(static function (MockObject $qb) use ($self): void {
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
            ->getCollectionManager(static function (MockObject $qb) use ($self): void {
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
            ->getCollectionManager(static function (MockObject $qb) use ($self): void {
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
            ->getCollectionManager(static function (MockObject $qb) use ($self): void {
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
            ->getCollectionManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    [$self->equalTo('c.context = :context')],
                    [$self->equalTo('c.enabled = :enabled')]
                )->willReturn($qb);
                $qb->expects($self->exactly(2))->method('setParameter')->withConsecutive(
                    [$self->equalTo('context'), $self->equalTo('contextA')],
                    [$self->equalTo('enabled'), $self->equalTo(false)]
                )->willReturn($qb);
            })
            ->getByContext('contextA', false);
    }

    private function getCollectionManager($qbCallback): CollectionManager
    {
        $em = $this->createEntityManagerMock($qbCallback, []);

        $registry = $this->getMockForAbstractClass(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($em);

        return new CollectionManager(BaseCollection::class, $registry);
    }
}
