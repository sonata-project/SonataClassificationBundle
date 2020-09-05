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

use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\Doctrine\Model\PageableManagerInterface;

/**
 * @method PagerInterface         getRootCategoriesPager(int $page = 1, int $limit = 25, array $criteria = [])
 * @method PagerInterface         getSubCategoriesPager(int $categoryId, int $page = 1, int $limit = 25, array $criteria = [])
 * @method CategoryInterface[]    getRootCategoriesForContext(ContextInterface|string|null $context)
 * @method CategoryInterface[]    getAllRootCategories(bool $loadChildren = true)
 * @method CategoryInterface[]    getRootCategoriesSplitByContexts(bool $loadChildren = true)
 * @method CategoryInterface[]    getCategories(ContextInterface|string|null $context)
 * @method CategoryInterface|null getBySlug(string $slug, ContextInterface|string|null $context, bool $enabled = true)
 */
interface CategoryManagerInterface extends ManagerInterface, PageableManagerInterface
{
}
