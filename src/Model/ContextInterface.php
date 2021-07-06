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

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string|null $name
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
     * @param string $id
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getId();

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
}
