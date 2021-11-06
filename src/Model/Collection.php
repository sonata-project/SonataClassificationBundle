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

abstract class Collection implements CollectionInterface
{
    private ?string $name = null;

    private ?string $slug = null;

    private bool $enabled = false;

    private ?string $description = null;

    private ?\DateTimeInterface $createdAt = null;

    private ?\DateTimeInterface $updatedAt = null;

    private ?ContextInterface $context = null;

    public function __toString(): string
    {
        return $this->getName() ?? 'n/a';
    }

    public function setName(?string $name): void
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

    final public function setContext(?ContextInterface $context): void
    {
        $this->context = $context;
    }

    final public function getContext(): ?ContextInterface
    {
        return $this->context;
    }
}
