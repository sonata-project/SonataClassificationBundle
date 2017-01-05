<?php

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
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Validator\Constraints\Valid;

class CategoryAdmin extends ContextAwareAdmin
{
    // NEXT_MAJOR: remove this override
    protected $formOptions = array(
        'cascade_validation' => true,
    );

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(RouteCollection $routes)
    {
        $routes->add('tree', 'tree');
    }

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        // NEXT_MAJOR: set constraints unconditionally
        if (isset($this->formOptions['cascade_validation'])) {
            unset($this->formOptions['cascade_validation']);
            $this->formOptions['constraints'][] = new Valid();
        } else {
            @trigger_error(<<<'EOT'
Unsetting cascade_validation is deprecated since 3.2, and will give an error in 4.0.
Override getFormBuilder() and remove the "Valid" constraint instead.
EOT
            , E_USER_DEPRECATED);
        }

        return parent::getFormBuilder();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-6'))
                ->add('name')
                ->add('description', 'textarea', array(
                    'required' => false,
                ))
        ;

        if ($this->hasSubject()) {
            if ($this->getSubject()->getParent() !== null || $this->getSubject()->getId() === null) { // root category cannot have a parent
                $formMapper
                  ->add('parent', 'sonata_category_selector', array(
                      'category' => $this->getSubject() ?: null,
                      'model_manager' => $this->getModelManager(),
                      'class' => $this->getClass(),
                      'required' => true,
                      'context' => $this->getSubject()->getContext(),
                    ));
            }
        }

        $position = $this->hasSubject() && !is_null($this->getSubject()->getPosition()) ? $this->getSubject()->getPosition() : 0;

        $formMapper
            ->end()
            ->with('Options', array('class' => 'col-md-6'))
                ->add('enabled', null, array(
                    'required' => false,
                ))
                ->add('position', 'integer', array(
                    'required' => false,
                    'data' => $position,
                ))
            ->end()
        ;

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $formMapper
                ->with('General')
                    ->add('media', 'sonata_type_model_list',
                        array(
                            'required' => false,
                        ),
                        array(
                            'link_parameters' => array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'sonata_category',
                            ),
                        )
                    )
                ->end();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        parent::configureDatagridFilters($datagridMapper);

        $datagridMapper
            ->add('name')
            ->add('enabled')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('context', null, array(
                'sortable' => 'context.name',
            ))
            ->add('slug')
            ->add('description')
            ->add('enabled', null, array('editable' => true))
            ->add('position')
            ->add('parent', null, array(
                'sortable' => 'parent.name',
            ))
        ;
    }
}
