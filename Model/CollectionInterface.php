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

interface CollectionInterface
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
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

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
