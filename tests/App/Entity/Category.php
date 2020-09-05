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

namespace Sonata\ClassificationBundle\Tests\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\ClassificationMediaBundle\Entity\BaseCategory;
use Sonata\MediaBundle\Model\MediaInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="classification__category")
 */
class Category extends BaseCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var MediaInterface|null
     */
    private $media;

    public function getId(): int
    {
        return $this->id;
    }

    public function setMedia(?MediaInterface $media = null): void
    {
        $this->media = $media;
    }

    public function getMedia(): ?MediaInterface
    {
        return $this->media;
    }
}
