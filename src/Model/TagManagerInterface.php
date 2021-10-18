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
 * @phpstan-extends ManagerInterface<TagInterface>
 */
interface TagManagerInterface extends ManagerInterface
{
    /**
     * @param ContextInterface|string|null $context
     */
    public function getBySlug(string $slug, $context = null, ?bool $enabled = true): ?TagInterface;

    /**
     * @param ContextInterface|string $context
     *
     * @return TagInterface[]
     */
    public function getByContext($context, ?bool $enabled = true): array;
}
