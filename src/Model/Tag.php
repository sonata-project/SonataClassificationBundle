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
    protected $enabled;

    /**
     * @var ContextInterface|null
     */
    protected $context;

    public function __toString()
    {
        return $this->getName() ?: 'n/a';
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setName($name)
    {
        $this->name = $name;

        $this->setSlug($name);
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setSlug($slug)
    {
        $this->slug = self::slugify($slug);
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setCreatedAt(?\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setUpdatedAt(?\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     *
     * @see http://snipplr.com/view/22741/slugify-a-string-in-php/.
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

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function setContext(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getContext()
    {
        return $this->context;
    }
}
