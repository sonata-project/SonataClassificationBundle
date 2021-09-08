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
    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $slug;

    /**
     * @var \DateTime|null
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     */
    protected $updatedAt;

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var ContextInterface|null
     */
    protected $context;

    public function __toString()
    {
        return $this->getName() ?: 'n/a';
    }

    public function setName($name): void
    {
        $this->name = $name;

        $this->setSlug($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setSlug($slug): void
    {
        $this->slug = self::slugify($slug);
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
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
     *
     * @param string $text
     *
     * @return string
     */
    public static function slugify($text)
    {
        $text = Slugify::create()->slugify($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function setContext(ContextInterface $context): void
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}
