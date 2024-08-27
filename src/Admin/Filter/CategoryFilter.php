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

namespace Sonata\ClassificationBundle\Admin\Filter;

use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\Type\Filter\DefaultType;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class CategoryFilter extends Filter
{
    public function __construct(private CategoryManagerInterface $categoryManager)
    {
    }

    public function filter(ProxyQueryInterface $query, string $alias, string $field, FilterData $data): void
    {
        if (!$data->hasValue() || null === $data->getValue()) {
            return;
        }

        $query
            ->getQueryBuilder()
            ->andWhere(\sprintf('%s.%s = :category', $alias, $field))
            ->setParameter('category', $data->getValue());

        $this->setActive(true);
    }

    public function getDefaultOptions(): array
    {
        return [
            'context' => null,
            'field_type' => ChoiceType::class,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getFormOptions(): array
    {
        return [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getOption('choices', [
                'choices' => $this->getChoices(),
                'choice_translation_domain' => false,
            ]),
            'label' => $this->getLabel(),
        ];
    }

    /**
     * @psalm-suppress DeprecatedClass
     *
     * NEXT_MAJOR: Remove this method.
     */
    public function getRenderSettings(): array
    {
        return [DefaultType::class, [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getOption('choices', [
                'choices' => $this->getChoices(),
                'choice_translation_domain' => false,
            ]),
            'label' => $this->getLabel(),
        ]];
    }

    /**
     * @return array<string, int>
     */
    private function getChoices(): array
    {
        $context = $this->getOption('context');

        if (null === $context) {
            $categories = $this->categoryManager->getAllRootCategories();
        } else {
            $categories = $this->categoryManager->getRootCategoriesForContext($context);
        }

        $choices = [];

        foreach ($categories as $category) {
            $catContext = $category->getContext();

            \assert(null !== $catContext);

            $choices[\sprintf('%s (%s)', $category->getName() ?? '', $catContext->getId() ?? '')] = $category->getId();

            $this->visitChild($category, $choices);
        }

        return $choices;
    }

    /**
     * @param array<string, mixed> $choices
     */
    private function visitChild(CategoryInterface $category, array &$choices, int $level = 2): void
    {
        if (0 === \count($category->getChildren())) {
            return;
        }

        foreach ($category->getChildren() as $child) {
            $choices[\sprintf('%s %s', str_repeat('-', 1 * $level), $child->__toString())] = $child->getId();

            $this->visitChild($child, $choices, $level + 1);
        }
    }
}
