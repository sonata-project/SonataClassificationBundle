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
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
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

    public function filter(ProxyQueryInterface $proxyQuery, string $alias, string $field, FilterData $data): void
    {
        if (!$data->hasValue() || null === $data->getValue()) {
            return;
        }

        $proxyQuery
            ->getQueryBuilder()
            ->andWhere(sprintf('%s.%s = :collection', $alias, $field))
            ->setParameter('collection', $data->getValue());

        $this->setActive(true);
    }

    public function getDefaultOptions(): array
    {
        return [
            'context' => null,
            'field_type' => ChoiceType::class,
        ];
    }

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

    protected function association(ProxyQueryInterface $queryBuilder, FilterData $data): array
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
        $contextId = $this->getOption('context');

        if (null === $contextId) {
            $collections = $this->collectionManager->findAll();
        } else {
            $collections = $this->collectionManager->getByContext($contextId);
        }

        $choices = [];

        foreach ($collections as $collection) {
            $choices[(string) $collection] = $collection->getId();
        }

        return $choices;
    }
}
