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

use Cocur\Slugify\Slugify;

abstract class Tag implements TagInterface
{
    protected ?string $name = null;

    protected ?string $slug = null;

    protected ?\DateTimeInterface $createdAt = null;

    protected ?\DateTimeInterface $updatedAt = null;

    protected bool $enabled = false;

    protected ?ContextInterface $context = null;

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
        $this->slug = self::slugify($slug ?? '');
    }

    final public function getSlug(): ?string
    {
        return $this->slug;
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

    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @see http://snipplr.com/view/22741/slugify-a-string-in-php/.
     */
    final public static function slugify(string $text): string
    {
        $text = Slugify::create()->slugify($text);

        if ('' === $text) {
            return 'n-a';
        }

        return $text;
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
