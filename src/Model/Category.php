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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sonata\MediaBundle\Model\MediaInterface;

abstract class Category implements CategoryInterface
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
     * @var int|null
     */
    protected $position;

    /**
     * @var Collection<int, CategoryInterface>
     */
    protected $children;

    /**
     * @var CategoryInterface|null
     */
    protected $parent;

    /**
     * @var MediaInterface|null
     */
    protected $media;

    /**
     * @var ContextInterface
     */
    protected $context;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ?: 'n/a';
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
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
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @deprecated only used by the AdminHelper
     */
    public function addChildren(CategoryInterface $child)
    {
        $this->addChild($child, true);
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function addChild(CategoryInterface $child, $nested = false)
    {
        $this->children[] = $child;

        if ($this->getContext()) {
            $child->setContext($this->getContext());
        }

        if (!$nested) {
            $child->setParent($this, true);
        }
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function removeChild(CategoryInterface $childToDelete)
    {
        foreach ($this->getChildren() as $pos => $child) {
            if ($childToDelete->getId() && $child->getId() === $childToDelete->getId()) {
                unset($this->children[$pos]);

                return;
            }

            if (!$childToDelete->getId() && $child === $childToDelete) {
                unset($this->children[$pos]);

                return;
            }
        }
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setChildren($children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $category) {
            $this->addChild($category);
        }
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function hasChildren()
    {
        return \count($this->children) > 0;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setParent(?CategoryInterface $parent = null, $nested = false)
    {
        $this->parent = $parent;

        if (!$nested && $parent) {
            $parent->addChild($this, true);
        }
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getParent()
    {
        return $this->parent;
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
