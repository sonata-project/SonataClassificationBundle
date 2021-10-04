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

use Doctrine\Persistence\ManagerRegistry;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\Pager\PagerInterface;
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
     * @param string $class
     */
    public function __construct($class, ManagerRegistry $registry, ContextManagerInterface $contextManager)
    {
        parent::__construct($class, $registry);

        $this->contextManager = $contextManager;
        $this->categories = [];
    }

    public function getRootCategoriesPager($page = 1, $limit = 25, $criteria = [])
    {
        $queryBuilder = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('parent')
            ->equals(null);

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuilder));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    public function getSubCategoriesPager($categoryId, $page = 1, $limit = 25, $criteria = [])
    {
        $queryBuilder = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('parent')
            ->equals($categoryId);

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuilder));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * NEXT_MAJOR: remove this method.
     *
     * @deprecated since sonata-project/classification-bundle 3.x, to be removed in 4.0.
     */
    public function getPager(array $criteria, int $page, int $limit = 10, array $sort = []): PagerInterface
    {
        $query = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('enabled')
            ->equals((bool) ($criteria['enabled'] ?? true));

        if (isset($criteria['context'])) {
            $query
                ->field('context')
                ->equals($criteria['context']);
        }

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    public function getRootCategoryWithChildren(CategoryInterface $category)
    {
        if (null === $category->getContext()) {
            throw new \InvalidArgumentException(sprintf(
                'Context of category "%s" cannot be null.',
                $category->getId()
            ));
        }
        if (null !== $category->getParent()) {
            throw new \InvalidArgumentException('Method can be called only for root categories.');
        }

        $context = $category->getContext();

        $this->loadCategories($context);

        foreach ($this->categories[$context->getId()] as $contextRootCategory) {
            if ($category->getId() === $contextRootCategory->getId()) {
                return $contextRootCategory;
            }
        }

        throw new \InvalidArgumentException(sprintf('Category "%s" does not exist.', $category->getId()));
    }

    public function getRootCategoriesForContext(?ContextInterface $context = null)
    {
        $context = $this->getContext($context);

        $this->loadCategories($context);

        return $this->categories[$context->getId()];
    }

    public function getAllRootCategories($loadChildren = true)
    {
        $rootCategories = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('parent')
            ->equals(null)
            ->getQuery()
            ->execute();

        $categories = [];

        foreach ($rootCategories as $category) {
            if (null === $category->getContext()) {
                throw new \LogicException(sprintf(
                    'Context of category "%s" cannot be null.',
                    $category->getId()
                ));
            }

            $categories[] = $loadChildren ? $this->getRootCategoryWithChildren($category) : $category;
        }

        return $categories;
    }

    public function getRootCategoriesSplitByContexts($loadChildren = true)
    {
        $rootCategories = $this->getAllRootCategories($loadChildren);

        $splitCategories = [];

        foreach ($rootCategories as $category) {
            $splitCategories[$category->getContext()->getId()][] = $category;
        }

        return $splitCategories;
    }

    public function getBySlug(string $slug, $context = null, ?bool $enabled = true): ?CategoryInterface
    {
        $queryBuilder = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('slug')
            ->equals($slug);

        if (null !== $context) {
            $queryBuilder
                ->field('context')
                ->equals($context);
        }
        if (null !== $enabled) {
            $queryBuilder
                ->field('enabled')
                ->equals($enabled);
        }

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * Load all categories from the database, the current method is very efficient for < 256 categories.
     */
    protected function loadCategories(ContextInterface $context): void
    {
        if (\array_key_exists($context->getId(), $this->categories)) {
            return;
        }

        $categories = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('context')
            ->equals($context->getId())
            ->sort('parent')
            ->getQuery()
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

        $root = null;

        foreach ($categories as $pos => $category) {
            if (0 === $pos && $category->getParent()) {
                throw new \LogicException('The first category must be the root.');
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

        if (null !== $root) {
            $this->categories[$context->getId()] = [
                0 => $root,
            ];
        }
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
