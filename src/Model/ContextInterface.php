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

interface ContextInterface
{
    public const DEFAULT_CONTEXT = 'default';

    public function setName(?string $name): void;

    public function getName(): ?string;

    public function setEnabled(bool $enabled): void;

    public function getEnabled(): bool;

    public function setId(?string $id): void;

    public function getId(): ?string;

    public function setCreatedAt(?\DateTimeInterface $createdAt): void;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void;

    public function getUpdatedAt(): ?\DateTimeInterface;
}
