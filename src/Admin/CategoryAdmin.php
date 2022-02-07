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
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\ClassificationBundle\Form\Type\CategorySelectorType;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @phpstan-extends ContextAwareAdmin<CategoryInterface>
 */
final class CategoryAdmin extends ContextAwareAdmin
{
    protected $classnameLabel = 'Category';

    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('tree', 'tree');
    }

    protected function configureFormOptions(array &$formOptions): void
    {
        $formOptions['constraints'][] = new Valid();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('general', ['class' => 'col-md-6'])
                ->add('name')
                ->add('description', TextareaType::class, [
                    'required' => false,
                ]);

        if ($this->hasSubject()) {
            if (null !== $this->getSubject()->getParent() || null === $this->getSubject()->getId()) { // root category cannot have a parent
                $form
                    ->add('parent', CategorySelectorType::class, [
                        'category' => $this->getSubject(),
                        'model_manager' => $this->getModelManager(),
                        'class' => $this->getClass(),
                        'required' => true,
                        'context' => $this->getSubject()->getContext(),
                    ]);
            }
        }

        $position = $this->hasSubject() && null !== $this->getSubject()->getPosition() ? $this->getSubject()->getPosition() : 0;

        $form
            ->end()
            ->with('options', ['class' => 'col-md-6'])
                ->add('enabled', CheckboxType::class, [
                    'required' => false,
                ])
                ->add('position', IntegerType::class, [
                    'required' => false,
                    'data' => $position,
                ])
            ->end();
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
            ->add('context', null, [
                'sortable' => 'context.name',
            ])
            ->add('slug')
            ->add('description')
            ->add('enabled', null, ['editable' => true])
            ->add('position')
            ->add('parent', null, [
                'sortable' => 'parent.name',
            ]);
    }
}
