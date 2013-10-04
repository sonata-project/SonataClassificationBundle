<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project <https://github.com/sonata-project/SonataClassificationBundle/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Model;

interface CollectionManagerInterface
{
    /**
     * Creates an empty category instance
     *
     * @return Category
     */
    public function create();

    /**
     * Deletes a collection
     *
     * @param CollectionInterface $collection
     *
     * @return void
     */
    public function delete(CollectionInterface $collection);

    /**
     * Finds one collection by the given criteria
     *
     * @param array $criteria
     *
     * @return CollectionInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Finds one category by the given criteria
     *
     * @param array $criteria
     *
     * @return CollectionInterface
     */
    public function findBy(array $criteria);

    /**
     * Returns the category's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Save a Collection
     *
     * @param CollectionInterface $collection
     *
     * @return void
     */
    public function save(CollectionInterface $collection);
}
