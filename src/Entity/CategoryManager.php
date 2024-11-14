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

use Doctrine\Persistence\ManagerRegistry;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\Doctrine\Entity\BaseEntityManager;

/**
 * @phpstan-extends BaseEntityManager<CategoryInterface>
 */
final class CategoryManager extends BaseEntityManager implements CategoryManagerInterface
{
    /**
     * @var array<string, CategoryInterface[]>
     */
    protected array $categories;

    /**
     * @phpstan-param class-string<CategoryInterface> $class
     */
    public function __construct(
        string $class,
        ManagerRegistry $registry,
        protected ContextManagerInterface $contextManager,
    ) {
        parent::__construct($class, $registry);
        $this->categories = [];
    }

    public function getRootCategoryWithChildren(CategoryInterface $category): CategoryInterface
    {
        $context = $category->getContext();
        if (null === $context) {
            throw new \InvalidArgumentException(\sprintf(
                'Context of category "%s" cannot be null.',
                $category->getId() ?? ''
            ));
        }

        $contextId = $context->getId();
        if (null === $contextId) {
            throw new \InvalidArgumentException(\sprintf(
                'Context of category "%s" must have an not null identifier.',
                $category->getId() ?? ''
            ));
        }

        if (null !== $category->getParent()) {
            throw new \InvalidArgumentException('Method can be called only for root categories.');
        }

        $this->loadCategories($context);

        foreach ($this->categories[$contextId] as $contextRootCategory) {
            if ($category->getId() === $contextRootCategory->getId()) {
                return $contextRootCategory;
            }
        }

        throw new \InvalidArgumentException(\sprintf('Category "%s" does not exist.', $category->getId() ?? ''));
    }

    public function getRootCategoriesForContext(?ContextInterface $context = null): array
    {
        if (null === $context) {
            $context = $this->getContext();
        }

        $contextId = $context->getId();
        if (null === $contextId) {
            throw new \InvalidArgumentException(\sprintf(
                'Context "%s" must have an not null identifier.',
                $context->getName() ?? ''
            ));
        }

        $this->loadCategories($context);

        return $this->categories[$contextId];
    }

    public function getAllRootCategories(bool $loadChildren = true): array
    {
        /** @var CategoryInterface[] $rootCategories */
        $rootCategories = $this->getRepository()
            ->createQueryBuilder('c')
            ->where('c.parent IS NULL')
            ->getQuery()
            ->getResult();

        if ([] === $rootCategories) {
            return $this->getRootCategoriesForContext(null);
        }

        $categories = [];

        foreach ($rootCategories as $category) {
            if (null === $category->getContext()) {
                throw new \LogicException(\sprintf(
                    'Context of category "%s" cannot be null.',
                    $category->getId() ?? ''
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
            $queryBuilder->andWhere('c.context = :context')->setParameter('context', $contextId);
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
        $contextId = $context->getId();
        if (null === $contextId || \array_key_exists($contextId, $this->categories)) {
            return;
        }

        /** @var CategoryInterface[] $categories */
        $categories = $this->getRepository()
            ->createQueryBuilder('c')
            ->where('c.context = :context')
            ->orderBy('c.parent')
            ->setParameter('context', $contextId)
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

            $categoryId = $category->getId();
            \assert(null !== $categoryId);
            $this->categories[$contextId][$categoryId] = $category;
        }

        $this->categories[$contextId] = $rootCategories;
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
