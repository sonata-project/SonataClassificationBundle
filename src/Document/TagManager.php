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

use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\Doctrine\Document\BaseDocumentManager;

/**
 * @phpstan-extends BaseDocumentManager<TagInterface>
 */
final class TagManager extends BaseDocumentManager implements TagManagerInterface
{
    public function getBySlug(string $slug, ?string $contextId = null, ?bool $enabled = true): ?TagInterface
    {
        $queryBuilder = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('slug')
            ->equals($slug);

        if (null !== $contextId) {
            $queryBuilder
                ->field('context')
                ->equals($contextId);
        }
        if (null !== $enabled) {
            $queryBuilder
                ->field('enabled')
                ->equals($enabled);
        }

        $tag = $queryBuilder->getQuery()->execute();

        \assert(null === $tag || $tag instanceof TagInterface);

        return $tag;
    }

    public function getByContext(string $contextId, ?bool $enabled = true): array
    {
        $queryBuilder = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('context')
            ->equals($contextId);

        if (null !== $enabled) {
            $queryBuilder
                ->field('enabled')
                ->equals($enabled);
        }

        $result = $queryBuilder->getQuery()->execute();

        \assert(null !== $result);

        return $result;
    }
}
