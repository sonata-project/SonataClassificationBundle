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

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Filter\DefaultType;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class CategoryFilter extends Filter
{
    /**
     * @var CategoryManagerInterface
     */
    private $categoryManager;

    public function __construct(CategoryManagerInterface $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data): void
    {
        if (null === $data || !\is_array($data) || !\array_key_exists('value', $data)) {
            return;
        }

        if (null !== $data['value']) {
            $queryBuilder
                ->andWhere(sprintf('%s.%s = :category', $alias, $field))
                ->setParameter('category', $data['value'])
            ;
        }

        $this->active = null !== $data['value'];
    }

    public function getDefaultOptions(): array
    {
        return [
            'context' => null,
        ];
    }

    public function getFieldType(): string
    {
        return $this->getOption('field_type', ChoiceType::class);
    }

    public function getFieldOptions(): array
    {
        return $this->getOption('choices', [
            'choices' => $this->getChoices(),
            'choice_translation_domain' => false,
        ]);
    }

    public function getRenderSettings(): array
    {
        return [DefaultType::class, [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'label' => $this->getLabel(),
        ]];
    }

    protected function association(ProxyQueryInterface $queryBuilder, $data): array
    {
        $alias = $queryBuilder->entityJoin($this->getParentAssociationMappings());
        $part = strrchr('.'.$this->getFieldName(), '.');
        $fieldName = substr(false === $part ? $this->getFieldType() : $part, 1);

        return [$alias, $fieldName];
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
            $categories = $this->categoryManager->getCategories($context);
        }

        $choices = [];

        foreach ($categories as $category) {
            $choices[sprintf('%s (%s)', $category->getName(), $category->getContext()->getId())] = $category->getId();

            $this->visitChild($category, $choices);
        }

        return $choices;
    }

    private function visitChild(CategoryInterface $category, array &$choices, int $level = 2): void
    {
        if (0 === \count($category->getChildren())) {
            return;
        }

        foreach ($category->getChildren() as $child) {
            $choices[sprintf('%s %s', str_repeat('-', 1 * $level), (string) $child)] = $child->getId();

            $this->visitChild($child, $choices, $level + 1);
        }
    }
}
