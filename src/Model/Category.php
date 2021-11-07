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

abstract class Category implements CategoryInterface
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

    final public function setName(?string $name): void
    {
        $this->name = $name;

        $this->setSlug($name);
    }

    final public function getName(): ?string
    {
        return $this->name;
    }

    final public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    final public function getEnabled(): bool
    {
        return $this->enabled;
    }

    final public function setSlug(?string $slug): void
    {
        $this->slug = Tag::slugify($slug ?? '');
    }

    final public function getSlug(): ?string
    {
        return $this->slug;
    }

    final public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    final public function getDescription(): ?string
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

    final public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    final public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    final public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    final public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    final public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    final public function getPosition(): ?int
    {
        return $this->position;
    }

    final public function addChild(CategoryInterface $child, bool $nested = false): void
    {
        $this->children[] = $child;

        if (null !== $this->getContext()) {
            $child->setContext($this->getContext());
        }

        if (!$nested) {
            $child->setParent($this, true);
        }
    }

    final public function removeChild(CategoryInterface $childToDelete): void
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

    final public function getChildren(): Collection
    {
        return $this->children;
    }

    final public function setChildren(array $children): void
    {
        $this->children = new ArrayCollection();

        foreach ($children as $category) {
            $this->addChild($category);
        }
    }

    final public function hasChildren(): bool
    {
        return \count($this->children) > 0;
    }

    final public function setParent(?CategoryInterface $parent = null, bool $nested = false): void
    {
        $this->parent = $parent;

        if (!$nested && null !== $parent) {
            $parent->addChild($this, true);
        }
    }

    final public function getParent(): ?CategoryInterface
    {
        return $this->parent;
    }

    final public function setContext(?ContextInterface $context): void
    {
        $this->context = $context;
    }

    final public function getContext(): ?ContextInterface
    {
        return $this->context;
    }
}
