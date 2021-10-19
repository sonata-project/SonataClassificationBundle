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

use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\Doctrine\Document\BaseDocumentManager;

final class CollectionManager extends BaseDocumentManager implements CollectionManagerInterface
{
    public function getBySlug(string $slug, ?string $context = null, ?bool $enabled = true): ?CollectionInterface
    {
        $queryBuilder = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('slug')
            ->equals($slug);

        if (null !== $context) {
            $queryBuilder
                ->field('context')
                ->equals($context);
        }
        if (null !== $enabled) {
            $queryBuilder
                ->field('enabled')
                ->equals($enabled);
        }

        $collection = $queryBuilder->getQuery()->execute();

        \assert(null === $collection || $collection instanceof CollectionInterface);

        return $collection;
    }

    public function getByContext(string $context, ?bool $enabled = true): array
    {
        $queryBuilder = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('context')
            ->equals($context);

        if (null !== $enabled) {
            $queryBuilder
                ->field('enabled')
                ->equals($enabled);
        }

        return $queryBuilder->getQuery()->execute();
    }
}
