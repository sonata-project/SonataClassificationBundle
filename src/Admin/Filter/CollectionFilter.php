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
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class CollectionFilter extends Filter
{
    /**
     * @var CollectionManagerInterface
     */
    private $collectionManager;

    public function __construct(CollectionManagerInterface $collectionManager)
    {
        $this->collectionManager = $collectionManager;
    }

    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data): void
    {
        if (null === $data || !\is_array($data) || !\array_key_exists('value', $data)) {
            return;
        }

        if ($data['value']) {
            $queryBuilder
                ->andWhere(sprintf('%s.%s = :collection', $alias, $field))
                ->setParameter('collection', $data['value'])
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
            $collections = $this->collectionManager->findAll();
        } else {
            $collections = $this->collectionManager->getByContext($context);
        }

        $choices = [];

        foreach ($collections as $collection) {
            $choices[(string) $collection] = $collection->getId();
        }

        return $choices;
    }
}
