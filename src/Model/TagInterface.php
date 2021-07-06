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

interface TagInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * @return string|null
     */
    public function getSlug();

    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt();

    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt();

    public function setContext(ContextInterface $context);

    /**
     * @return ContextInterface|null
     */
    public function getContext();
}
