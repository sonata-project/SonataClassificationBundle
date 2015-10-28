<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project <https://github.com/sonata-project/SonataClassificationBundle/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Model;

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
     * @return int $slug
     */
    public function getSlug();

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get description.
     *
     * @return string $description
     */
    public function getDescription();

    /**
     * @param int $position
     */
    public function setPosition($position);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * Add Children.
     *
     * @param CategoryInterface $children
     * @param bool              $nested
     */
    public function addChild(CategoryInterface $children, $nested = false);

    /**
     * Get Children.
     *
     * @return Collection $children
     */
    public function getChildren();

    /**
     * Set children.
     *
     * @param $children
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
     * @param CategoryInterface $parent
     * @param bool              $nested
     */
    public function setParent(CategoryInterface $parent = null, $nested = false);

    /**
     * Get Parent.
     *
     * @return CategoryInterface $parent
     */
    public function getParent();

    /**
     * @param MediaInterface $media
     */
    public function setMedia(MediaInterface $media = null);

    /**
     * @return MediaInterface
     */
    public function getMedia();

    /**
     * @param ContextInterface $context
     */
    public function setContext(ContextInterface $context);

    /**
     * @return ContextInterface
     */
    public function getContext();
}
