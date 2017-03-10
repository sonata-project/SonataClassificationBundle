<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Form\ChoiceList;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

final class CategoryChoiceLoader implements ChoiceLoaderInterface
{
    /**
     * The loaded choice list.
     *
     * @var ArrayChoiceList
     */
    private $choiceList;

    /**
     * @var array
     */
    private $choices;

    /**
     * @param array $choices
     */
    public function __construct($choices)
    {
        $this->choices = $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        if (null !== $this->choiceList) {
            return $this->choiceList;
        }

        return $this->choiceList = new ArrayChoiceList($this->choices, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        if (empty($values)) {
            return array();
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        if (empty($choices)) {
            return array();
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
