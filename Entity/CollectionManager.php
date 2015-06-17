<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

class CollectionManager extends BaseEntityManager implements CollectionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = array())
    {
        $parameters = array();

        $query = $this->getRepository()
            ->createQueryBuilder('c')
            ->select('c');

        if (isset($criteria['enabled'])) {
            $query->andWhere('c.enabled = :enabled');
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
}
