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
 * @phpstan-extends ManagerInterface<CollectionInterface>
 */
interface CollectionManagerInterface extends ManagerInterface
{
    public function getBySlug(string $slug, ?string $context = null, ?bool $enabled = true): ?CollectionInterface;

    /**
     * @return CollectionInterface[]
     */
    public function getByContext(string $context, ?bool $enabled = true): array;
}
