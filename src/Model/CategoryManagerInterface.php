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
use Sonata\DatagridBundle\Pager\PageableInterface;
use Sonata\Doctrine\Model\ManagerInterface;

/**
 * @phpstan-extends ManagerInterface<CategoryInterface>
 * @phpstan-extends PageableInterface<CategoryInterface>
 */
interface CategoryManagerInterface extends ManagerInterface, PageableInterface
{
    /**
     * Returns a pager to iterate over the root category.
     *
     * @param int   $page
     * @param int   $limit
     * @param array $criteria
     *
     * @return PagerInterface
     */
    public function getRootCategoriesPager($page = 1, $limit = 25, $criteria = []);

    /**
     * @param int   $categoryId
     * @param int   $page
     * @param int   $limit
     * @param array $criteria
     *
     * @return PagerInterface
     */
    public function getSubCategoriesPager($categoryId, $page = 1, $limit = 25, $criteria = []);

    /**
     * @return CategoryInterface
     */
    public function getRootCategoryWithChildren(CategoryInterface $category);

    /**
     * @param ContextInterface $context
     *
     * @return CategoryInterface[]
     */
    public function getRootCategoriesForContext(?ContextInterface $context = null);

    /**
     * @param bool|true $loadChildren
     *
     * @return CategoryInterface[]
     */
    public function getAllRootCategories($loadChildren = true);

    /**
     * @param bool|true $loadChildren
     *
     * @return array
     */
    public function getRootCategoriesSplitByContexts($loadChildren = true);

    /**
     * @param ContextInterface|string|null $context
     *
     * @return CategoryInterface|null
     */
    public function getBySlug(string $slug, $context = null, ?bool $enabled = true);
}
