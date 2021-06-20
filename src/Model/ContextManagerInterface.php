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

use Sonata\DatagridBundle\Pager\PageableInterface;
use Sonata\Doctrine\Model\ManagerInterface;

/**
 * @phpstan-extends ManagerInterface<ContextInterface>
 */
interface ContextManagerInterface extends ManagerInterface, PageableInterface
{
}
