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
        return $this->getName() ?: 'n/a';
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
        $this->slug = self::slugify($slug ?? '');
    }

    public function getSlug(): ?string
    {
        return $this->slug;
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

    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @see http://snipplr.com/view/22741/slugify-a-string-in-php/.
     *
     * @static
     */
    public static function slugify(string $text): string
    {
        $text = Slugify::create()->slugify($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
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
