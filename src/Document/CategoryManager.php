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
use Sonata\Doctrine\Document\BaseDocumentManager;

/**
 * @phpstan-extends BaseDocumentManager<CategoryInterface>
 */
final class CategoryManager extends BaseDocumentManager implements CategoryManagerInterface
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
        $context = $this->getContext($context);

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
        $rootCategories = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('parent')
            ->equals(null)
            ->getQuery()
            ->execute();
        \assert(\is_array($rootCategories));

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
            $catContext = $category->getContext();
            \assert(null !== $catContext);

            $splitCategories[(string) $catContext->getId()][] = $category;
        }

        return $splitCategories;
    }

    public function getBySlug(string $slug, ?string $contextId = null, ?bool $enabled = true): ?CategoryInterface
    {
        $queryBuilder = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('slug')
            ->equals($slug);

        if (null !== $contextId) {
            $queryBuilder
                ->field('context')
                ->equals($contextId);
        }
        if (null !== $enabled) {
            $queryBuilder
                ->field('enabled')
                ->equals($enabled);
        }

        $category = $queryBuilder->getQuery()->execute();

        \assert(null === $category || $category instanceof CategoryInterface);

        return $category;
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

        $categories = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('context')
            ->equals($context->getId())
            ->sort('parent')
            ->getQuery()
            ->execute();
        \assert(\is_array($categories));

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
            if (0 === $pos && null !== $category->getParent()) {
                throw new \LogicException('The first category must be the root.');
            }

            if (0 === $pos) {
                $root = $category;
            }

            $categoryId = $category->getId();
            \assert(null !== $categoryId);
            $this->categories[$contextId][$categoryId] = $category;
        }

        if (null !== $root) {
            $this->categories[$contextId] = [
                0 => $root,
            ];
        }
    }

    private function getContext(ContextInterface|string|null $context): ContextInterface
    {
        if (null === $context) {
            $context = ContextInterface::DEFAULT_CONTEXT;
        }

        if ($context instanceof ContextInterface) {
            return $context;
        }

        $contextModel = $this->contextManager->find($context);

        if (!$contextModel instanceof ContextInterface) {
            $contextModel = $this->contextManager->create();

            $contextModel->setId($context);
            $contextModel->setName($context);
            $contextModel->setEnabled(true);

            $this->contextManager->save($contextModel);
        }

        return $contextModel;
    }
}
