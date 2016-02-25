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

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var string
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
     * @var int
     */
    protected $position;

    /**
     * @var CategoryInterface[]
     */
    protected $children;

    /**
     * @var CategoryInterface
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

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        $this->setSlug($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug)
    {
        $this->slug = Tag::slugify($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName() ?: 'n/a';
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
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
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
     * @param CategoryInterface $child
     *
     * @deprecated only used by the AdminHelper
     */
    public function addChildren(CategoryInterface $child)
    {
        $this->addChild($child, true);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function setChildren($children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $category) {
            $this->addChild($category);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(CategoryInterface $parent = null, $nested = false)
    {
        $this->parent = $parent;

        if (!$nested && $parent) {
            $parent->addChild($this, true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setMedia(MediaInterface $media = null)
    {
        $this->media = $media;
    }

    /**
     * {@inheritdoc}
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }
}
