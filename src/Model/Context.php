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

abstract class Context implements ContextInterface
{
    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $name;

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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @final since sonata-project/classification-bundle 3.18
     */
    public function getId()
    {
        return $this->id;
    }
}
