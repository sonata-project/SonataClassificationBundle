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
use Sonata\ClassificationBundle\Model\TagInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * @phpstan-extends ContextAwareAdmin<TagInterface>
 */
final class TagAdmin extends ContextAwareAdmin
{
    protected $classnameLabel = 'Tag';

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name')
            ->add('context');

        if ($this->hasSubject() && null !== $this->getSubject()->getId()) {
            $form->add('slug');
        }

        $form->add('enabled', CheckboxType::class, [
            'required' => false,
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        parent::configureDatagridFilters($filter);

        $filter
            ->add('name')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name')
            ->add('slug')
            ->add('context', null, [
                'sortable' => 'context.name',
            ])
            ->add('enabled', null, ['editable' => true])
            ->add('createdAt')
            ->add('updatedAt');
    }
}
