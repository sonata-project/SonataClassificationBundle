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

use JMS\Serializer\Context;
use JMS\Serializer\VisitorInterface;
use Sonata\CoreBundle\Serializer\BaseSerializerHandler;

/**
 * @author Thomas Rabaix <thomas.rabaix@gmail.com>
 */
class ContextSerializerHandler extends BaseSerializerHandler
{
    /**
     * Serialize data object to id.
     *
     * @param VisitorInterface $visitor
     * @param object           $data
     * @param array            $type
     * @param Context          $context
     *
     * @return int|null
     */
    public function serializeObjectToId(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $className = $this->manager->getClass();

        if ($data instanceof $className) {
            return $visitor->visitString($data->getId(), $type, $context);
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public static function getType()
    {
        return 'sonata_classification_context_id';
    }
}
