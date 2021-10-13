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

namespace Sonata\ClassificationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\Doctrine\Entity\BaseEntityManager;
use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

/**
 * @phpstan-extends BaseEntityManager<CategoryInterface>
 */
final class CategoryManager extends BaseEntityManager implements CategoryManagerInterface
{
    /**
     * @var array<string, CategoryInterface[]>
     */
    protected array $categories;

    protected ContextManagerInterface $contextManager;

    /**
     * @phpstan-param class-string<CategoryInterface> $class
     */
    public function __construct(string $class, ManagerRegistry $registry, ContextManagerInterface $contextManager)
    {
        parent::__construct($class, $registry);

        $this->contextManager = $contextManager;
        $this->categories = [];
    }

    /**
     * Returns a pager to iterate over the root category.
     */
    public function getRootCategoriesPager(int $page = 1, int $limit = 25, array $criteria = []): Pager
    {
        $page = 0 === $page ? 1 : $page;

        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from($this->class, 'c')
            ->andWhere('c.parent IS NULL');

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuilder));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    public function getSubCategoriesPager($categoryId, int $page = 1, int $limit = 25, array $criteria = []): Pager
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
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

    public function getRootCategoryWithChildren(CategoryInterface $category): CategoryInterface
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

    public function getRootCategoriesForContext(?ContextInterface $context = null): array
    {
        if (null === $context) {
            $context = $this->getContext();
        }

        $this->loadCategories($context);

        return $this->categories[$context->getId()];
    }

    public function getAllRootCategories(bool $loadChildren = true): array
    {
        $rootCategories = $this->getRepository()
            ->createQueryBuilder('c')
            ->where('c.parent IS NULL')
            ->getQuery()
            ->getResult();

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

    public function getRootCategoriesSplitByContexts(bool $loadChildren = true): array
    {
        $rootCategories = $this->getAllRootCategories($loadChildren);

        $splitCategories = [];

        foreach ($rootCategories as $category) {
            $context = $category->getContext();

            \assert(null !== $context);

            $splitCategories[(string) $context->getId()][] = $category;
        }

        return $splitCategories;
    }

    public function getBySlug(string $slug, ?string $contextId = null, ?bool $enabled = true): ?CategoryInterface
    {
        $queryBuilder = $this->getRepository()
            ->createQueryBuilder('c')
            ->select('c')
            ->andWhere('c.slug = :slug')->setParameter('slug', $slug);

        if (null !== $contextId) {
            $queryBuilder->andWhere('c.context = :context')->setParameter('context', $contextId, Types::OBJECT);
        }
        if (null !== $enabled) {
            $queryBuilder->andWhere('c.enabled = :enabled')->setParameter('enabled', $enabled);
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Load all categories from the database, the current method is very efficient for < 256 categories.
     */
    protected function loadCategories(ContextInterface $context): void
    {
        if (\array_key_exists($context->getId(), $this->categories)) {
            return;
        }

        $categories = $this->getRepository()
            ->createQueryBuilder('c')
            ->where('c.context = :context')
            ->orderBy('c.parent')
            ->setParameter('context', $context->getId())
            ->getQuery()
            ->getResult();

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

        $rootCategories = [];
        foreach ($categories as $category) {
            if (null === $category->getParent()) {
                $rootCategories[] = $category;
            }

            $this->categories[(string) $context->getId()][$category->getId()] = $category;

            $parent = $category->getParent();

            $category->disableChildrenLazyLoading();

            if ($parent) {
                $parent->addChild($category);
            }
        }

        $this->categories[(string) $context->getId()] = $rootCategories;
    }

    private function getContext(): ContextInterface
    {
        $contextObj = $this->contextManager->find(ContextInterface::DEFAULT_CONTEXT);

        if (!$contextObj instanceof ContextInterface) {
            $contextObj = $this->contextManager->create();

            $contextObj->setId(ContextInterface::DEFAULT_CONTEXT);
            $contextObj->setName(ContextInterface::DEFAULT_CONTEXT);
            $contextObj->setEnabled(true);

            $this->contextManager->save($contextObj);
        }

        return $contextObj;
    }
}
