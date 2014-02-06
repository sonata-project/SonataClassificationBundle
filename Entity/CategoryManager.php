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

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\CoreBundle\Entity\DoctrineBaseManager;
use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Datagrid\PagerInterface;

class CategoryManager extends DoctrineBaseManager
{
    /**
     * @var array
     */
    protected $categories;

    /**
     * Returns a pager to iterate over the root category
     *
     * @param integer $page
     * @param integer $limit
     * @param array   $criteria
     *
     * @return mixed
     */
    public function getRootCategoriesPager($page = 1, $limit = 25, $criteria = array())
    {
        $page = (int) $page == 0 ? 1 : (int) $page;

        $queryBuiler = $this->om->createQueryBuilder()
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
     * @param integer $categoryId
     * @param integer $page
     * @param integer $limit
     * @param array   $criteria
     *
     * @return PagerInterface
     */
    public function getSubCategoriesPager($categoryId, $page = 1, $limit = 25, $criteria = array())
    {
        $queryBuiler = $this->om->createQueryBuilder()
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
     * @return CategoryInterface
     */
    public function getRootCategory()
    {
        $this->loadCategories();

        return $this->categories[0];
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        $this->loadCategories();

        return $this->categories;
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

        $categories = $this->om->createQuery(sprintf('SELECT c FROM %s c INDEX BY c.id', $class))
            ->execute();

        foreach ($categories as $category) {
            $this->categories[$category->getId()] = $category;

            $parent = $category->getParent();

            $category->disableChildrenLazyLoading();

            if (!$parent) {
                $root->addChild($category, true);

                continue;
            }

            $parent->addChild($category);
        }
    }
}
