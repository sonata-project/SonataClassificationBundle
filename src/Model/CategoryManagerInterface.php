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

namespace Sonata\ClassificationBundle\Model;

use Sonata\AdminBundle\Datagrid\PagerInterface;
use Sonata\Doctrine\Model\ManagerInterface;

/**
 * @phpstan-extends ManagerInterface<CategoryInterface>
 */
interface CategoryManagerInterface extends ManagerInterface
{
    /**
     * Returns a pager to iterate over the root category.
     *
     * @phpstan-param array<string, mixed> $criteria
     */
    public function getRootCategoriesPager(int $page = 1, int $limit = 25, array $criteria = []): PagerInterface;

    /**
     * @param mixed $categoryId
     *
     * @phpstan-param array<string, mixed> $criteria
     */
    public function getSubCategoriesPager($categoryId, int $page = 1, int $limit = 25, array $criteria = []): PagerInterface;

    public function getRootCategoryWithChildren(CategoryInterface $category): CategoryInterface;

    /**
     * @return CategoryInterface[]
     */
    public function getRootCategoriesForContext(?ContextInterface $context = null): array;

    /**
     * @return CategoryInterface[]
     */
    public function getAllRootCategories(bool $loadChildren = true): array;

    /**
     * @return array<string, CategoryInterface[]>
     */
    public function getRootCategoriesSplitByContexts(bool $loadChildren = true): array;

    public function getBySlug(string $slug, ?string $contextId = null, ?bool $enabled = true): ?CategoryInterface;
}
