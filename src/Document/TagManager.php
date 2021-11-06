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

use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Sonata\Doctrine\Document\BaseDocumentManager;

/**
 * @final since sonata-project/classification-bundle 3.18
 */
class TagManager extends BaseDocumentManager implements TagManagerInterface
{
    /**
     * NEXT_MAJOR: remove this method.
     *
     * @deprecated since sonata-project/classification-bundle 3.18, to be removed in 4.0.
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        $query = $this->getDocumentManager()
            ->createQueryBuilder($this->getClass())
            ->field('enabled')
            ->equals($criteria['enabled'] ?? true);

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
