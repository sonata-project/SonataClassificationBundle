<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project <https://github.com/sonata-project/SonataClassificationBundle/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Model\CategoryManager as ModelCategoryManager;
use Sonata\ClassificationBundle\Model\CategoryInterface;

use Doctrine\ORM\EntityManager;
use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class CategoryManager extends ModelCategoryManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    protected $categories;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string                      $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function save(CategoryInterface $category)
    {
        $this->em->persist($category);
        $this->em->flush();

        $this->categories = null;
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(CategoryInterface $category)
    {
        $this->em->remove($category);
        $this->em->flush();

        $this->categories = null;
    }

    /**
     * {@inheritDoc}
     */
    public function getRootCategoriesPager($page = 1, $limit = 25, $criteria = array())
    {
        $page = (int) $page == 0 ? 1 : (int) $page;

        $queryBuiler = $this->em->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->andWhere('c.parent IS NULL');

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuiler));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubCategoriesPager($categoryId, $page = 1, $limit = 25, $criteria = array())
    {
        $queryBuiler = $this->em->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.parent = :categoryId')
            ->setParameter('categoryId', $categoryId);

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuiler));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * {@inheritDoc}
     */
    public function getRootCategory()
    {
        $this->loadCategories();

        return $this->categories[0];
    }

    /**
     * Load all categories from the database, the current method is very efficient for < 256 categories
     *
     */
    protected function loadCategories()
    {
        if ($this->categories !== null) {
            return;
        }

        $class = $this->getClass();

        $root = $this->create();
        $root->setName('root');

        $this->categories = array(
            0 => $root
        );

        $categories = $this->em->createQuery(sprintf('SELECT c FROM %s c INDEX BY c.id', $class))
            ->execute();

        foreach ($categories as $category) {
            $this->categories[$category->getId()] = $category;

            $parent = $category->getParent();

            $category->disableChildrenLazyLoading();

            if (!$parent) {
                $root->addChild($category);

                continue;
            }

            $parent->addChild($category);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getCategories()
    {
        $this->loadCategories();

        return $this->categories;
    }
}
