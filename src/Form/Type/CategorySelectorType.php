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
use Sonata\ClassificationBundle\Model\ContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * @psalm-suppress MissingTemplateParam https://github.com/phpstan/phpstan-symfony/issues/320
 */
final class CategorySelectorType extends AbstractType
{
    public function __construct(private CategoryManagerInterface $manager)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'context' => null,
            'category' => null,
            'choice_loader' => fn (Options $opts): ChoiceLoaderInterface => new CategoryChoiceLoader(array_flip($this->getChoices($opts))),
        ]);
    }

    /**
     * @return array<array-key, string>
     *
     * @phpstan-param Options<array{
     *     context: ContextInterface|null,
     *     category: CategoryInterface|null,
     * }> $options
     * @psalm-param Options $options
     */
    public function getChoices(Options $options): array
    {
        $category = $options['category'];

        if (null === $category) {
            return [];
        }

        $context = $options['context'];

        if (null === $context) {
            $rootCategories = $this->manager->getAllRootCategories();
        } else {
            $rootCategories = $this->manager->getRootCategoriesForContext($context);
        }

        $choices = [];

        foreach ($rootCategories as $rootCategory) {
            $context = $rootCategory->getContext();
            $rootCategoryId = $rootCategory->getId();
            \assert(null !== $context && null !== $rootCategoryId);

            $choices[$rootCategoryId] = \sprintf('%s (%s)', $rootCategory->getName() ?? '', $context->getId() ?? '');

            $this->childWalker($rootCategory, $category, $choices);
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
    private function childWalker(CategoryInterface $rootCategory, CategoryInterface $category, array &$choices, int $level = 2): void
    {
        if ($rootCategory->getChildren()->isEmpty()) {
            return;
        }

        foreach ($rootCategory->getChildren() as $childCategory) {
            $childCategoryId = $childCategory->getId();
            \assert(null !== $childCategoryId);

            if ($category->getId() === $childCategoryId) {
                continue;
            }

            $choices[$childCategoryId] = \sprintf('%s %s', str_repeat('-', 1 * $level), $childCategory->getName() ?? '');

            $this->childWalker($childCategory, $category, $choices, $level + 1);
        }
    }
}
