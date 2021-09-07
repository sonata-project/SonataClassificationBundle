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

use Sonata\MediaBundle\Model\MediaInterface;

/**
 * @method mixed getId()
 */
interface CollectionInterface
{
    // NEXT_MAJOR: Uncomment this method.
    // /**
    // * @return mixed
    // */
    // public function getId();

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
     * @return bool $enabled
     */
    public function getEnabled();

    /**
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * @return string|null $slug
     */
    public function getSlug();

    /**
     * @param string|null $description
     */
    public function setDescription($description);

    /**
     * @return string|null $description
     */
    public function getDescription();

    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt();

    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt();

    public function setMedia(?MediaInterface $media = null);

    /**
     * @return MediaInterface|null
     */
    public function getMedia();

    public function setContext(ContextInterface $context);

    /**
     * @return ContextInterface|null
     */
    public function getContext();
}
