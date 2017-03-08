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

use Sonata\ClassificationBundle\Entity\BaseCategory;
use Sonata\ClassificationBundle\Entity\BaseContext;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\CoreBundle\Test\EntityManagerMockFactory;

abstract class CategoryTest extends BaseCategory
{
    private $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}

abstract class ContextTest extends BaseContext
{
    public function getId()
    {
        return $this->id;
    }
}

class CategoryManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPager()
    {
        $self = $this;
        $this
            ->getCategoryManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array()));
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
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array()));
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    array($self->equalTo('c.context = :context')),
                    array($self->equalTo('c.enabled = :enabled'))
                );
                $qb->expects($self->once())->method('setParameters')->with(array('enabled' => true, 'context' => 'default'));
            })
            ->getPager(array(
                'enabled' => true,
                'context' => 'default',
            ), 1);
    }

    public function testGetPagerWithDisabledCategories()
    {
        $self = $this;
        $this
            ->getCategoryManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array()));
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    array($self->equalTo('c.context = :context')),
                    array($self->equalTo('c.enabled = :enabled'))
                );
                $qb->expects($self->once())->method('setParameters')->with(array('enabled' => false, 'context' => 'default'));
            })
            ->getPager(array(
                'enabled' => false,
                'context' => 'default',
            ), 1);
    }

    public function testGetCategoriesWithMultipleRootsInContext()
    {
        /** @var ContextTest $context */
        $context = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\ContextTest');
        $context->setId(1);
        $context->setName('default');
        $context->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($context);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        /** @var CategoryTest $categoryBar */
        $categoryBar = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($context);
        $categoryBar->setParent(null);
        $categoryBar->setEnabled(true);

        $categories = array($categoryFoo, $categoryBar);

        $categoryManager = $this->getCategoryManager(function ($qb) {
        }, $categories);

        $this->assertSame($categoryManager->getCategories($context), $categories);
    }

    public function testGetRootCategoryWithChildren()
    {
        /** @var ContextTest $context */
        $context = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\ContextTest');
        $context->setId(1);
        $context->setName('default');
        $context->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($context);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        /** @var CategoryTest $categoryBar */
        $categoryBar = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($context);
        $categoryBar->setParent($categoryFoo);
        $categoryBar->setEnabled(true);

        $categoryManager = $this->getCategoryManager(function ($qb) {
        }, array($categoryFoo, $categoryBar));

        $categoryFoo = $categoryManager->getRootCategoryWithChildren($categoryFoo);
        $this->assertContains($categoryBar, $categoryFoo->getChildren());
    }

    public function testGetRootCategory()
    {
        /** @var ContextTest $context */
        $context = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\ContextTest');
        $context->setId(1);
        $context->setName('default');
        $context->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($context);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        $categoryManager = $this->getCategoryManager(function ($qb) {
        }, array($categoryFoo));

        $categoryBar = $categoryManager->getRootCategory($context);
        $this->assertEquals($categoryFoo, $categoryBar);
    }

    public function testGetRootCategoriesForContext()
    {
        /** @var ContextTest $context */
        $context = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\ContextTest');
        $context->setId(1);
        $context->setName('default');
        $context->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($context);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        /** @var CategoryTest $categoryBar */
        $categoryBar = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($context);
        $categoryBar->setParent($categoryFoo);
        $categoryBar->setEnabled(true);

        $categoryManager = $this->getCategoryManager(function ($qb) {
        }, array($categoryFoo, $categoryBar));

        $categories = $categoryManager->getRootCategoriesForContext($context);
        $this->assertCount(1, $categories);
        $this->assertContains($categoryFoo, $categories);
    }

    public function testGetRootCategories()
    {
        /** @var ContextTest $contextFoo */
        $contextFoo = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\ContextTest');
        $contextFoo->setId(1);
        $contextFoo->setName('foo');
        $contextFoo->setEnabled(true);

        /** @var ContextTest $contextBar */
        $contextBar = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\ContextTest');
        $contextBar->setId(2);
        $contextBar->setName('bar');
        $contextBar->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($contextFoo);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        /** @var CategoryTest $categoryBar */
        $categoryBar = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($contextBar);
        $categoryBar->setParent(null);
        $categoryBar->setEnabled(true);

        $categoryManager = $this->getCategoryManager(function ($qb) {
        }, array($categoryFoo, $categoryBar));

        $categories = $categoryManager->getRootCategories(false);
        $this->assertArrayHasKey($contextFoo->getId(), $categories);
        $this->assertArrayHasKey($contextBar->getId(), $categories);
        $this->assertEquals($categoryFoo, $categories[$contextFoo->getId()]);
        $this->assertEquals($categoryBar, $categories[$contextBar->getId()]);
    }

    public function testGetRootCategoriesSplitByContexts()
    {
        /** @var ContextTest $contextFoo */
        $contextFoo = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\ContextTest');
        $contextFoo->setId(1);
        $contextFoo->setName('foo');
        $contextFoo->setEnabled(true);

        /** @var ContextTest $contextBar */
        $contextBar = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\ContextTest');
        $contextBar->setId(2);
        $contextBar->setName('bar');
        $contextBar->setEnabled(true);

        /** @var CategoryTest $categoryFoo */
        $categoryFoo = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryFoo->setId(1);
        $categoryFoo->setName('foo');
        $categoryFoo->setContext($contextFoo);
        $categoryFoo->setParent(null);
        $categoryFoo->setEnabled(true);

        /** @var CategoryTest $categoryBar */
        $categoryBar = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Tests\Entity\CategoryTest');
        $categoryBar->setId(2);
        $categoryBar->setName('bar');
        $categoryBar->setContext($contextBar);
        $categoryBar->setParent(null);
        $categoryBar->setEnabled(true);

        $categoryManager = $this->getCategoryManager(function ($qb) {
        }, array($categoryFoo, $categoryBar));

        $categories = $categoryManager->getRootCategoriesSplitByContexts(false);
        $this->assertArrayHasKey($contextFoo->getId(), $categories);
        $this->assertArrayHasKey($contextBar->getId(), $categories);
        $this->assertContains($categoryFoo, $categories[$contextFoo->getId()]);
        $this->assertContains($categoryBar, $categories[$contextBar->getId()]);
    }

    protected function getCategoryManager($qbCallback, $createQueryResult = null)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, array());

        if (null != $createQueryResult) {
            $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')->disableOriginalConstructor()->getMock();
            $query->expects($this->once())->method('execute')->will($this->returnValue($createQueryResult));
            $query->expects($this->any())->method('setParameter')->will($this->returnValue($query));
            $em->expects($this->once())->method('createQuery')->will($this->returnValue($query));
        }

        $registry = $this->getMockForAbstractClass('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $contextManager = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Model\ContextManagerInterface');

        return new CategoryManager('Sonata\PageBundle\Entity\BaseCategory', $registry, $contextManager);
    }
}
