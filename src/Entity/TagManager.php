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

use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\Doctrine\Entity\BaseEntityManager;

final class TagManager extends BaseEntityManager implements TagManagerInterface
{
    public function getBySlug(string $slug, ?string $contextId = null, ?bool $enabled = true): ?TagInterface
    {
        $queryBuilder = $this->getRepository()
            ->createQueryBuilder('t')
            ->select('t')
            ->andWhere('t.slug = :slug')->setParameter('slug', $slug);

        if (null !== $contextId) {
            $queryBuilder->andWhere('t.context = :context')->setParameter('context', $contextId);
        }
        if (null !== $enabled) {
            $queryBuilder->andWhere('t.enabled = :enabled')->setParameter('enabled', $enabled);
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getByContext(string $contextId, ?bool $enabled = true): array
    {
        $queryBuilder = $this->getRepository()
            ->createQueryBuilder('t')
            ->select('t')
            ->andWhere('t.context = :context')->setParameter('context', $contextId);

        if (null !== $enabled) {
            $queryBuilder->andWhere('t.enabled = :enabled')->setParameter('enabled', $enabled);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
