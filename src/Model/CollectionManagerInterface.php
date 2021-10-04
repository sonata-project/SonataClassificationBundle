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
 * @method CollectionInterface|null getBySlug(string $slug, ContextInterface|string|null $context, bool $enabled = true)
 * @method CollectionInterface[]    getByContext(ContextInterface|string $context, bool $enabled = true)
 *
 * @phpstan-extends ManagerInterface<CollectionInterface>
 */
interface CollectionManagerInterface extends ManagerInterface
{
}
