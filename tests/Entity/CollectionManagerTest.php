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

    protected function getCollectionManager($qbCallback)
    {
        $em = $this->createEntityManagerMock($qbCallback, []);

        $registry = $this->getMockForAbstractClass(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->willReturn($em);

        return new CollectionManager(BaseCollection::class, $registry);
    }
}
