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

namespace Sonata\ClassificationBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Valid;

class CollectionAdmin extends ContextAwareAdmin
{
    protected $classnameLabel = 'Collection';

    /**
     * @param array<string, mixed> $formOptions
     */
    protected function configureFormOptions(array &$formOptions): void
    {
        $formOptions['constraints'][] = new Valid();
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name')
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('context')
            ->add('enabled', CheckboxType::class, [
                'required' => false,
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        parent::configureDatagridFilters($datagridMapper);

        $datagridMapper
            ->add('name')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('name')
            ->add('slug')
            ->add('context', null, [
                'sortable' => 'context.name',
            ])
            ->add('enabled', null, [
                'editable' => true,
            ]);
    }
}
