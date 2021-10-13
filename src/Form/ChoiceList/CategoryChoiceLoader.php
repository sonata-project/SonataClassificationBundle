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

namespace Sonata\ClassificationBundle\Form\ChoiceList;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

final class CategoryChoiceLoader implements ChoiceLoaderInterface
{
    /**
     * The loaded choice list.
     */
    private ?ArrayChoiceList $choiceList = null;

    /**
     * @var array<string, mixed>
     */
    private array $choices;

    /**
     * @param array<string, mixed> $choices
     */
    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    public function loadChoiceList($value = null)
    {
        if (null !== $this->choiceList) {
            return $this->choiceList;
        }

        return $this->choiceList = new ArrayChoiceList($this->choices, $value);
    }

    public function loadChoicesForValues(array $values, $value = null): array
    {
        if (empty($values)) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    public function loadValuesForChoices(array $choices, $value = null): array
    {
        if (empty($choices)) {
            return [];
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
