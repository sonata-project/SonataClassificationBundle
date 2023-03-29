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

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * @phpstan-extends AbstractAdmin<ContextInterface>
 */
final class ContextAdmin extends AbstractAdmin
{
    protected $classnameLabel = 'Context';

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->ifTrue(!($this->hasSubject() && null !== $this->getSubject()->getId()))
                ->add('id')
            ->ifEnd()
            ->add('name')
            ->add('enabled', CheckboxType::class, [
                'required' => false,
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('name')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name')
            ->addIdentifier('id')
            ->add('enabled', null, [
                'editable' => true,
            ])
            ->add('createdAt')
            ->add('updatedAt')
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'translation_domain' => 'SonataAdminBundle',
                'actions' => [
                    'edit' => [],
                ],
            ]);
    }
}
