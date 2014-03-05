<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Model;

use Sonata\CoreBundle\Model\ManagerInterface;

interface CategoryManagerInterface extends ManagerInterface
{
    /**
     * Retrieve categories, based on the criteria, a page at a time.
     *
     * Valid criteria are:
     *    enabled - boolean
     *
     * @param array   $criteria
     * @param integer $page
     * @param integer $maxPerPage
     *
     * @return \Sonata\AdminBundle\Datagrid\Pager
     */
    public function getPager(array $criteria, $page, $maxPerPage = 10);
}
