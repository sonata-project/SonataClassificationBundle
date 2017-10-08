<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Tests\Entity;

use Sonata\ClassificationBundle\Entity\CollectionManager;
use Sonata\ClassificationBundle\Tests\Helpers\PHPUnit_Framework_TestCase;
use Sonata\CoreBundle\Test\EntityManagerMockFactory;

class CollectionManagerTest extends PHPUnit_Framework_TestCase
{
    public function testGetPager()
    {
        $self = $this;
        $this
            ->getCollectionManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue([]));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with([]);
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithEnabledCollections()
    {
        $self = $this;
        $this
            ->getCollectionManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue([]));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('c.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameters')->with(['enabled' => true]);
            })
            ->getPager([
                'enabled' => true,
            ], 1);
    }

    public function testGetPagerWithDisabledCollections()
    {
        $self = $this;
        $this
            ->getCollectionManager(function ($qb) use ($self) {
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
        $em = EntityManagerMockFactory::create($this, $qbCallback, []);

        $registry = $this->getMockForAbstractClass('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new CollectionManager('Sonata\PageBundle\Entity\BaseCollection', $registry);
    }
}
