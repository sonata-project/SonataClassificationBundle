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

namespace Sonata\ClassificationBundle\Document;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Sonata\Doctrine\Document\BaseDocumentManager;

class CategoryManager extends BaseDocumentManager implements CategoryManagerInterface
{
    /**
     * @var CategoryInterface[]
     */
    protected $categories;

    /**
     * @var ContextManagerInterface
     */
    protected $contextManager;

    /**
     * @param string                  $class
     * @param ManagerRegistry         $registry
     * @param ContextManagerInterface $contextManager
     */
    public function __construct($class, ManagerRegistry $registry, ContextManagerInterface $contextManager)
    {
        parent::__construct($class, $registry);

        $this->contextManager = $contextManager;
        $this->categories = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRootCategoriesPager($page = 1, $limit = 25, $criteria = [])
    {
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
     * {@inheritdoc}
     */
    public function getSubCategoriesPager($categoryId, $page = 1, $limit = 25, $criteria = [])
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
     * {@inheritdoc}
     */
    public function getRootCategory($context = null)
    {
        $context = $this->getContext($context);

        $this->loadCategories($context);

        return current($this->categories[$context->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRootCategories($loadChildren = true)
    {
        $rootCategories = $this->getObjectManager()->createQuery(sprintf('SELECT c FROM %s c WHERE c.parent IS NULL', $this->getClass()))
            ->execute();

        $categories = [];

        foreach ($rootCategories as $category) {
            if (null === $category->getContext()) {
                throw new \RuntimeException('Context cannot be null');
            }

            $categories[$category->getContext()->getId()] = $loadChildren ? $this->getRootCategory($category->getContext()) : $category;
        }

        return $categories;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories($context = null)
    {
        $context = $this->getContext($context);

        $this->loadCategories($context);

        return $this->categories[$context->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        $parameters = [];

        $query = $this->getRepository()
            ->createQueryBuilder('c')
            ->select('c');

        if (isset($criteria['context'])) {
            $query->andWhere('c.context = :context');
            $parameters['context'] = $criteria['context'];
        }

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

    /**
     * {@inheritdoc}
     */
    public function getRootCategoryWithChildren(CategoryInterface $category): void
    {
        throw new \RuntimeException('Not Implemented yet');
    }

    /**
     * {@inheritdoc}
     */
    public function getRootCategoriesForContext(ContextInterface $context = null): void
    {
        throw new \RuntimeException('Not Implemented yet');
    }

    /**
     * {@inheritdoc}
     */
    public function getAllRootCategories($loadChildren = true): void
    {
        throw new \RuntimeException('Not Implemented yet');
    }

    /**
     * {@inheritdoc}
     */
    public function getRootCategoriesSplitByContexts($loadChildren = true): void
    {
        throw new \RuntimeException('Not Implemented yet');
    }

    /**
     * Load all categories from the database, the current method is very efficient for < 256 categories.
     *
     * @param ContextInterface $context
     */
    protected function loadCategories(ContextInterface $context): void
    {
        if (array_key_exists($context->getId(), $this->categories)) {
            return;
        }

        $categories = $this->getObjectManager()->createQuery(sprintf('SELECT c FROM %s c WHERE c.context = :context ORDER BY c.parent ASC', $this->getClass()))
            ->setParameter('context', $context->getId())
            ->execute();

        if (0 === \count($categories)) {
            // no category, create one for the provided context
            $category = $this->create();
            $category->setName($context->getName());
            $category->setEnabled(true);
            $category->setContext($context);
            $category->setDescription($context->getName());

            $this->save($category);

            $categories = [$category];
        }

        foreach ($categories as $pos => $category) {
            if (0 === $pos && $category->getParent()) {
                throw new \RuntimeException('The first category must be the root');
            }

            if (0 === $pos) {
                $root = $category;
            }

            $this->categories[$context->getId()][$category->getId()] = $category;

            $parent = $category->getParent();

            $category->disableChildrenLazyLoading();

            if ($parent) {
                $parent->addChild($category);
            }
        }

        $this->categories[$context->getId()] = [
            0 => $root,
        ];
    }

    /**
     * @param ContextInterface|string $context
     *
     * @return ContextInterface
     */
    private function getContext($context)
    {
        if (empty($context)) {
            $context = ContextInterface::DEFAULT_CONTEXT;
        }

        if ($context instanceof ContextInterface) {
            return $context;
        }

        $context = $this->contextManager->find($context);

        if (!$context instanceof ContextInterface) {
            $context = $this->contextManager->create();

            $context->setId($context);
            $context->setName($context);
            $context->setEnabled(true);

            $this->contextManager->save($context);
        }

        return $context;
    }
}
