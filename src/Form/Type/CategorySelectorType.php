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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Select a category.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class CategorySelectorType extends AbstractType
{
    protected CategoryManagerInterface $manager;

    public function __construct(CategoryManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function configureOptions(OptionsResolver $resolver): void
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
     * @return array<array-key, string>
     */
    public function getChoices(Options $options): array
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
            $context = $category->getContext();
            $categoryId = $category->getId();
            \assert(null !== $context && null !== $categoryId);

            $choices[$categoryId] = sprintf('%s (%s)', $category->getName() ?? '', $context->getId() ?? '');

            $this->childWalker($category, $options, $choices);
        }

        return $choices;
    }

    public function getParent(): string
    {
        return ModelType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sonata_category_selector';
    }

    /**
     * @param array<array-key, string> $choices
     */
    private function childWalker(CategoryInterface $category, Options $options, array &$choices, int $level = 2): void
    {
        if ($category->getChildren()->isEmpty()) {
            return;
        }

        foreach ($category->getChildren() as $child) {
            $childId = $child->getId();
            \assert(null !== $childId);

            if ($options['category'] instanceof CategoryInterface && $options['category']->getId() === $childId) {
                continue;
            }

            $choices[$childId] = sprintf('%s %s', str_repeat('-', 1 * $level), $child->getName() ?? '');

            $this->childWalker($child, $options, $choices, $level + 1);
        }
    }
}
