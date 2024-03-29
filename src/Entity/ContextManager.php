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

use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\Doctrine\Entity\BaseEntityManager;

/**
 * @phpstan-extends BaseEntityManager<ContextInterface>
 */
final class ContextManager extends BaseEntityManager implements ContextManagerInterface
{
}
