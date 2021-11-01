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

use Doctrine\ORM\PersistentCollection;
use Sonata\ClassificationBundle\Model\Category as ModelCategory;

abstract class BaseCategory extends ModelCategory
{
    /**
     * @deprecated since sonata-project/classification-bundle 3.x, to be removed in 4.0.
     */
    public function disableChildrenLazyLoading()
    {
        @trigger_error(
            sprintf(
                'The method %s is deprecated since sonata-project/classification-bundle 3.x and will be removed in 4.0.',
                __METHOD__
            ),
            \E_USER_DEPRECATED
        );

        if ($this->children instanceof PersistentCollection) {
            $this->children->setInitialized(true);
        }
    }
}
