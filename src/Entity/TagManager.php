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
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Sonata\Doctrine\Entity\BaseEntityManager;

class TagManager extends BaseEntityManager implements TagManagerInterface
{
    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        $parameters = [];

        $query = $this->getRepository()
            ->createQueryBuilder('t')
            ->select('t');

        if (isset($criteria['enabled'])) {
            $query->andWhere('t.enabled = :enabled');
            $parameters['enabled'] = (bool) $criteria['enabled'];
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    public function getBySlug(string $slug, $context = null, ?bool $enabled = true): ?TagInterface
    {
        $queryBuilder = $this->getRepository()
            ->createQueryBuilder('t')
            ->select('t')
            ->andWhere('t.slug = :slug')->setParameter('slug', $slug);

        if (null !== $context) {
            $queryBuilder->andWhere('t.context = :context')->setParameter('context', $context);
        }
        if (null !== $enabled) {
            $queryBuilder->andWhere('t.enabled = :enabled')->setParameter('enabled', $enabled);
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getByContext($context, ?bool $enabled = true): array
    {
        $queryBuilder = $this->getRepository()
            ->createQueryBuilder('t')
            ->select('t')
            ->andWhere('t.context = :context')->setParameter('context', $context);

        if (null !== $enabled) {
            $queryBuilder->andWhere('t.enabled = :enabled')->setParameter('enabled', $enabled);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
