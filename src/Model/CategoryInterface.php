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

use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Sonata\MediaBundle\Model\MediaInterface;

interface CategoryInterface
{
    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string $name
     */
    public function getName();

    /**
     * Set enabled.
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get enabled.
     *
     * @return bool $enabled
     */
    public function getEnabled();

    /**
     * Set slug.
     *
     * @param int $slug
     */
    public function setSlug($slug);

    /**
     * Get slug.
     *
     * @return string $slug
     */
    public function getSlug();

    /**
     * Set description.
     *
     * @param string|null $description
     */
    public function setDescription($description);

    /**
     * Get description.
     *
     * @return string|null $description
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
     * Add Children.
     *
     * @param CategoryInterface $children
     * @param bool              $nested
     */
    public function addChild(self $children, $nested = false);

    /**
     * Get Children.
     *
     * @return DoctrineCollection|CategoryInterface[] $children
     */
    public function getChildren();

    /**
     * Set children.
     *
     * @param array $children
     */
    public function setChildren($children);

    /**
     * Return true if category has children.
     *
     * @return bool
     */
    public function hasChildren();

    /**
     * Set Parent.
     *
     * @param CategoryInterface|null $parent
     * @param bool                   $nested
     */
    public function setParent(?self $parent = null, $nested = false);

    /**
     * Get Parent.
     *
     * @return CategoryInterface|null $parent
     */
    public function getParent();

    /**
     * @param MediaInterface $media
     */
    public function setMedia(?MediaInterface $media = null);

    /**
     * @return MediaInterface
     */
    public function getMedia();

    public function setContext(ContextInterface $context);

    /**
     * @return ContextInterface
     */
    public function getContext();
}
