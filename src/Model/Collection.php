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

abstract class Collection implements CollectionInterface
{
    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $slug;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var \DateTime|null
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     */
    protected $updatedAt;

    /**
     * @var MediaInterface|null
     */
    protected $media;

    /**
     * @var ContextInterface|null
     */
    protected $context;

    public function __toString()
    {
        return $this->getName() ?: 'n/a';
    }

    public function setName($name)
    {
        $this->name = $name;

        $this->setSlug($name);
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setSlug($slug)
    {
        $this->slug = Tag::slugify($slug);
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setMedia(?MediaInterface $media = null)
    {
        $this->media = $media;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setContext(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getContext()
    {
        return $this->context;
    }
}
