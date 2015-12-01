<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Form\Type;

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\CoreBundle\Model\ManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Select a category.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class CategorySelectorType extends AbstractType
{
    /**
     * @var CategoryManagerInterface
     */
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $that = $this;

        $resolver->setDefaults(array(
            'context'           => null,
            'category'          => null,
            'choice_list'       => function (Options $opts, $previousValue) use ($that) {
                return new SimpleChoiceList($that->getChoices($opts));
            },
        ));
    }

    /**
     * @param Options $options
     *
     * @return array
     */
    public function getChoices(Options $options)
    {
        if (!$options['category'] instanceof CategoryInterface) {
            return array();
        }

        if ($options['context'] === null) {
            $categories = $this->manager->getRootCategories();
        } else {
            $categories = array($this->manager->getRootCategory($options['context']));
        }

        $choices = array();

        foreach ($categories as $category) {
            $choices[$category->getId()] = sprintf('%s (%s)', $category->getName(), $category->getContext()->getId());

            $this->childWalker($category, $options, $choices);
        }

        return $choices;
    }

    /**
     * @param CategoryInterface $category
     * @param Options           $options
     * @param array             $choices
     * @param int               $level
     */
    private function childWalker(CategoryInterface $category, Options $options, array &$choices, $level = 2)
    {
        if ($category->getChildren() === null) {
            return;
        }

        foreach ($category->getChildren() as $child) {
            if ($options['category'] && $options['category']->getId() == $child->getId()) {
                continue;
            }

            $choices[$child->getId()] = sprintf('%s %s', str_repeat('-', 1 * $level), $child);

            $this->childWalker($child, $options, $choices, $level + 1);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'sonata_type_model';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sonata_category_selector';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
