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

namespace Sonata\ClassificationBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\VisitorInterface;
use Sonata\Form\Serializer\BaseSerializerHandler;

/**
 * NEXT_MAJOR: Remove this file.
 *
 * @author Thomas Rabaix <thomas.rabaix@gmail.com>
 *
 * @deprecated since sonata-project/classification-bundle 3.18, to be removed in 4.0.
 */
class ContextSerializerHandler extends BaseSerializerHandler
{
    public function serializeObjectToId(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $className = $this->manager->getClass();

        if ($data instanceof $className && $visitor instanceof SerializationVisitorInterface) {
            return $visitor->visitString($data->getId(), $type);
        }

        return null;
    }

    public static function getType()
    {
        return 'sonata_classification_context_id';
    }
}
