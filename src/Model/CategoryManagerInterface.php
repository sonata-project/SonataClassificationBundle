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

use Sonata\Doctrine\Model\ManagerInterface;

/**
 * @phpstan-extends ManagerInterface<CategoryInterface>
 */
interface CategoryManagerInterface extends ManagerInterface
{
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
