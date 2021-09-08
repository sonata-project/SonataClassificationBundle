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

namespace Sonata\ClassificationBundle\Model;

use Doctrine\Common\Collections\Collection;

interface CategoryInterface
{
    public function __toString(): string;

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string|null $name
     */
    public function getName();

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * @return string|null
     */
    public function getSlug();

    /**
     * @param string|null $description
     */
    public function setDescription($description);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @param int|null $position
     */
    public function setPosition($position);

    /**
     * @return int|null
     */
    public function getPosition();

    /**
     * @param bool $nested
     */
    public function addChild(self $children, $nested = false);

    /**
     * @return Collection<int, self> $children
     */
    public function getChildren();

    /**
     * @param iterable<self> $children
     */
    public function setChildren($children);

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @param bool $nested
     */
    public function setParent(?self $parent = null, $nested = false);

    /**
     * @return self|null
     */
    public function getParent();

    public function setContext(ContextInterface $context);

    /**
     * @return ContextInterface|null
     */
    public function getContext();
}
