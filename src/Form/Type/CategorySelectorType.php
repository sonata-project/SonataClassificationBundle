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

namespace Sonata\ClassificationBundle\Form\Type;

use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\ClassificationBundle\Form\ChoiceList\CategoryChoiceLoader;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\Doctrine\Model\ManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
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

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/classification-bundle 3.10, to be removed in version 4.0.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'context' => null,
            'category' => null,
            'choice_loader' => function (Options $opts): ChoiceLoaderInterface {
                return new CategoryChoiceLoader(array_flip($this->getChoices($opts)));
            },
        ]);
    }

    /**
     * @return array
     */
    public function getChoices(Options $options)
    {
        if (!$options['category'] instanceof CategoryInterface) {
            return [];
        }

        if (null === $options['context']) {
            $categories = $this->manager->getAllRootCategories();
        } else {
            $categories = $this->manager->getRootCategoriesForContext($options['context']);
        }

        $choices = [];

        foreach ($categories as $category) {
            $choices[$category->getId()] = sprintf('%s (%s)', $category->getName(), $category->getContext()->getId());

            $this->childWalker($category, $options, $choices);
        }

        return $choices;
    }

    public function getParent()
    {
        return ModelType::class;
    }

    public function getBlockPrefix()
    {
        return 'sonata_category_selector';
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/classification-bundle 3.10, to be removed in version 4.0.
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @param int $level
     */
    private function childWalker(CategoryInterface $category, Options $options, array &$choices, $level = 2)
    {
        if (null === $category->getChildren()) {
            return;
        }

        foreach ($category->getChildren() as $child) {
            if ($options['category'] && $options['category']->getId() === $child->getId()) {
                continue;
            }

            $choices[$child->getId()] = sprintf('%s %s', str_repeat('-', 1 * $level), $child);

            $this->childWalker($child, $options, $choices, $level + 1);
        }
    }
}
