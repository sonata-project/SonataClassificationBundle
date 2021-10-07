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

use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\Doctrine\Document\BaseDocumentManager;

/**
 * @final since sonata-project/classification-bundle 3.x
 */
class CollectionManager extends BaseDocumentManager implements CollectionManagerInterface
{
}
