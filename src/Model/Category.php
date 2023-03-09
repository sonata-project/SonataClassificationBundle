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

abstract class Category implements CategoryInterface, \Stringable
{
    protected ?string $name = null;

    protected ?string $slug = null;

    protected bool $enabled = false;

    protected ?string $description = null;

    protected ?\DateTimeInterface $createdAt = null;

    protected ?\DateTimeInterface $updatedAt = null;

    protected ?int $position = null;

    /**
     * @var Collection<int, CategoryInterface>
     */
    protected Collection $children;

    protected ?CategoryInterface $parent = null;

    protected ?ContextInterface $context = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName() ?? 'n/a';
    }

    public function setName(?string $name): void
    {
        $this->name = $name;

        $this->setSlug($name);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = Tag::slugify($slug ?? '');
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function prePersist(): void
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTime());
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function addChild(CategoryInterface $child, bool $nested = false): void
    {
        $this->children[] = $child;

        if (null !== $this->getContext()) {
            $child->setContext($this->getContext());
        }

        if (!$nested) {
            $child->setParent($this, true);
        }
    }

    public function removeChild(CategoryInterface $childToDelete): void
    {
        foreach ($this->getChildren() as $pos => $child) {
            if (null !== $childToDelete->getId() && $child->getId() === $childToDelete->getId()) {
                unset($this->children[$pos]);

                return;
            }

            if (null === $childToDelete->getId() && $child === $childToDelete) {
                unset($this->children[$pos]);

                return;
            }
        }
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setChildren(array $children): void
    {
        $this->children = new ArrayCollection();

        foreach ($children as $category) {
            $this->addChild($category);
        }
    }

    public function hasChildren(): bool
    {
        return \count($this->children) > 0;
    }

    public function setParent(?CategoryInterface $parent = null, bool $nested = false): void
    {
        $this->parent = $parent;

        if (!$nested && null !== $parent) {
            $parent->addChild($this, true);
        }
    }

    public function getParent(): ?CategoryInterface
    {
        return $this->parent;
    }

    public function setContext(?ContextInterface $context): void
    {
        $this->context = $context;
    }

    public function getContext(): ?ContextInterface
    {
        return $this->context;
    }
}
