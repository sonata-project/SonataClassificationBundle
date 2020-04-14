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
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Sonata\MediaBundle\Model\MediaInterface;

abstract class Category implements CategoryInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
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
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var int|null
     */
    protected $position;

    /**
     * @var DoctrineCollection|CategoryInterface[]
     */
    protected $children;

    /**
     * @var CategoryInterface|null
     */
    protected $parent;

    /**
     * @var MediaInterface
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

    public function setName($name)
    {
        $this->name = $name;

        $this->setSlug($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setSlug($slug)
    {
        $this->slug = Tag::slugify($slug);
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

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

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
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

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $category) {
            $this->addChild($category);
        }
    }

    public function hasChildren()
    {
        return \count($this->children) > 0;
    }

    public function setParent(?CategoryInterface $parent = null, $nested = false)
    {
        $this->parent = $parent;

        if (!$nested && $parent) {
            $parent->addChild($this, true);
        }
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setMedia(?MediaInterface $media = null)
    {
        $this->media = $media;
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function setContext(ContextInterface $context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}
