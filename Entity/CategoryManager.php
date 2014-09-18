<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Entity;

use Sonata\AdminBundle\Datagrid\PagerInterface;

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;

use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;

use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

class CategoryManager extends BaseEntityManager implements CategoryManagerInterface
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

        $queryBuilder = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->andWhere('c.parent IS NULL');

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuilder));
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
        $queryBuilder = $this->getObjectManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->where('c.parent = :categoryId')
            ->setParameter('categoryId', $categoryId);

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuilder));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * @param ContextInterface $context
     *
     * @return CategoryInterface
     */
    public function getRootCategory(ContextInterface $context = null)
    {
        $code = $context ? $context->getName() : ContextInterface::DEFAULT_CONTEXT;

        $this->loadCategories($code);

        return $this->categories[$code][0];
    }

    /**
     * @param ContextInterface $context
     *
     * @return array
     */
    public function getCategories(ContextInterface $context = null)
    {
        $code = $context ? $context->getName() : ContextInterface::DEFAULT_CONTEXT;

        $this->loadCategories($code);

        return $this->categories[$code];
    }

    /**
     * Load all categories from the database, the current method is very efficient for < 256 categories
     *
     */
    protected function loadCategories($code)
    {
        if (array_key_exists($code, $this->categories)) {
            return;
        }

        $this->categories[$code] = array();

        $class = $this->getClass();

        $root = $this->create();
        $root->setName('root');

        $this->categories = array(
            0 => $root
        );

        $categories = $this->getObjectManager()->createQuery(sprintf('SELECT c FROM %s c INDEX BY c.id WHERE context = :context', $class))
            ->setParameter('context', $code)
            ->execute();

        foreach ($categories as $category) {
            $this->categories[$code][$category->getId()] = $category;

            $parent = $category->getParent();

            $category->disableChildrenLazyLoading();

            if (!$parent) {
                $root->addChild($category, true);

                continue;
            }

            $parent->addChild($category);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = array())
    {
        $parameters = array();

        $query = $this->getRepository()
            ->createQueryBuilder('c')
            ->select('c');

        $query->andWhere('c.context = :context');

        $parameters['context'] = isset($criteria['context']) ? $criteria['context'] : ContextInterface::DEFAULT_CONTEXT;

        if (isset($criteria['enabled'])) {
            $query->andWhere('c.enabled = :enabled');
            $parameters['enabled'] = (bool) $criteria['enabled'];
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
