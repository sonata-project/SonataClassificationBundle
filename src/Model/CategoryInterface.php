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

use Doctrine\Common\Collections\Collection;

interface CategoryInterface extends ContextAwareInterface
{
    public function __toString(): string;

    /**
     * @return mixed
     */
    public function getId();

    public function setName(?string $name): void;

    public function getName(): ?string;

    public function setEnabled(bool $enabled): void;

    public function getEnabled(): bool;

    public function setSlug(?string $slug): void;

    public function getSlug(): ?string;

    public function setDescription(?string $description): void;

    public function getDescription(): ?string;

    public function setPosition(?int $position): void;

    public function getPosition(): ?int;

    public function addChild(self $child, bool $nested = false): void;

    /**
     * @return Collection<int, self> $children
     */
    public function getChildren(): Collection;

    /**
     * @param CategoryInterface[] $children
     */
    public function setChildren(array $children): void;

    public function hasChildren(): bool;

    public function setParent(?self $parent = null, bool $nested = false): void;

    public function getParent(): ?self;
}
