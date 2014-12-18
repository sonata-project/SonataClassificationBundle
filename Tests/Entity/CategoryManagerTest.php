<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Tests\Entity;

use Sonata\ClassificationBundle\Entity\CategoryManager;

/**
 * Class CategoryManagerTest
 *
 */
class CategoryManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function getCategoryManager($qbCallback)
    {
        $query = $this->getMockForAbstractClass('Doctrine\ORM\AbstractQuery', array(), '', false, true, true, array('execute'));
        $query->expects($this->any())->method('execute')->will($this->returnValue(true));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $qb->expects($this->any())->method('select')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('getQuery')->will($this->returnValue($query));

        $qbCallback($qb);

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($qb));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');

        return new CategoryManager('Sonata\PageBundle\Entity\BaseCategory', $registry, $contextManager);
    }

    public function testGetPager()
    {
        $self = $this;
        $this
            ->getCategoryManager(function ($qb) use ($self) {
                $qb->expects($self->exactly(1))->method('andWhere')->withConsecutive(
                    array($self->equalTo('c.context = :context'))
                );
                $qb->expects($self->once())->method('setParameters')->with(array('context' => 'default'));
            })
            ->getPager(array('context' => 'default'), 1);
    }

    public function testGetPagerWithEnabledCategories()
    {
        $self = $this;
        $this
            ->getCategoryManager(function ($qb) use ($self) {
                /** @var $self \PHPUnit_Framework_TestCase */
                /** @var $qb \PHPUnit_Framework_MockObject_InvocationMocker */
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    array($self->equalTo('c.context = :context')),
                    array($self->equalTo('c.enabled = :enabled'))
                );
                $qb->expects($self->once())->method('setParameters')->with(array('enabled' => true, 'context' => 'default'));
            })
            ->getPager(array(
                'enabled' => true,
                'context' => 'default'
            ), 1);
    }

    public function testGetPagerWithDisabledCategories()
    {
        $self = $this;
        $this
            ->getCategoryManager(function ($qb) use ($self) {
                /** @var $self \PHPUnit_Framework_TestCase */
                /** @var $qb \PHPUnit_Framework_MockObject_InvocationMocker */
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    array($self->equalTo('c.context = :context')),
                    array($self->equalTo('c.enabled = :enabled'))
                );
                $qb->expects($self->once())->method('setParameters')->with(array('enabled' => false, 'context' => 'default'));
            })
            ->getPager(array(
                'enabled' => false,
                'context' => 'default'
            ), 1);
    }
}
