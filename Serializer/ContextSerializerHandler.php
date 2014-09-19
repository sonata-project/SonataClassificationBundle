<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Serializer;

use Sonata\CoreBundle\Serializer\BaseSerializerHandler;

/**
 * @author Thomas Rabaix <thomas.rabaix@gmail.com>
 */
class ContextSerializerHandler extends BaseSerializerHandler
{
    /**
     * {@inheritdoc}
     */
    public static function getType()
    {
        return 'sonata_classification_context_id';
    }
}
