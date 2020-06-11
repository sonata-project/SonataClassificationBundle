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
use JMS\Serializer\VisitorInterface;
use Sonata\Form\Serializer\BaseSerializerHandler;

/**
 * @author Thomas Rabaix <thomas.rabaix@gmail.com>
 */
class ContextSerializerHandler extends BaseSerializerHandler
{
    /**
     * Serialize data object to id.
     *
     * @param object $data
     *
     * @return int|null
     */
    public function serializeObjectToId(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $className = $this->manager->getClass();

        if ($data instanceof $className) {
            return $visitor->visitString($data->getId(), $type, $context);
        }
    }

    public static function getType()
    {
        return 'sonata_classification_context_id';
    }
}
