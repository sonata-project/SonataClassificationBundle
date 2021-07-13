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

namespace Sonata\ClassificationBundle\Tests\Admin\Filter;

use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder as BaseQueryBuilder;

final class QueryBuilder extends BaseQueryBuilder
{
    public $parameters = [];

    public $query = [];

    public function __construct()
    {
    }

    public function setParameter($key, $value, $type = null)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function andWhere()
    {
        $query = \func_get_args();

        $this->query[] = $query;

        return $this;
    }

    public function expr(): self
    {
        return $this;
    }

    /**
     * @param string|string[] $parameter
     */
    public function in(string $alias, $parameter): string
    {
        if (\is_array($parameter)) {
            return sprintf('%s IN ("%s")', $alias, implode(', ', $parameter));
        }

        return sprintf('%s IN %s', $alias, $parameter);
    }

    public function getDQLPart($queryPart)
    {
        return [];
    }

    public function getRootAlias(): string
    {
        return current(($this->getRootAliases()));
    }

    public function leftJoin($join, $alias, $conditionType = null, $condition = null, $indexBy = null)
    {
        $this->query[] = $join;

        return $this;
    }

    public function orX($x = null): Orx
    {
        return new Orx(\func_get_args());
    }

    public function andX($x = null): Andx
    {
        return new Andx(\func_get_args());
    }

    public function neq(string $alias, string $parameter): string
    {
        return sprintf('%s <> %s', $alias, $parameter);
    }

    public function isNull(string $queryPart): string
    {
        return $queryPart.' IS NULL';
    }

    public function isNotNull(string $queryPart): string
    {
        return $queryPart.' IS NOT NULL';
    }

    /**
     * @param string|string[] $parameter
     */
    public function notIn(string $alias, $parameter): string
    {
        if (\is_array($parameter)) {
            return sprintf('%s NOT IN ("%s")', $alias, implode(', ', $parameter));
        }

        return sprintf('%s NOT IN %s', $alias, $parameter);
    }

    public function getAllAliases(): array
    {
        return $this->getRootAliases();
    }

    public function getRootAliases(): array
    {
        return ['o'];
    }
}
