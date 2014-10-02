<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project <https://github.com/sonata-project/SonataClassificationBundle/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\ClassificationBundle\Entity\ContextManager;
use Sonata\ClassificationBundle\Model\ContextInterface;

class CategoryAdmin extends Admin
{
    protected $formOptions = array(
        'cascade_validation' => true
    );

    protected $contextManager;

    /**
     * @param string         $code
     * @param string         $class
     * @param string         $baseControllerName
     * @param ContextManager $contextManager
     */
    public function __construct($code, $class, $baseControllerName, ContextManager $contextManager)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->contextManager = $contextManager;
    }

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
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();

        if ($contextId = $this->getPersistentParameter('context')) {
            $context = $this->contextManager->find($contextId);

            if (!$context) {
                $context = $this->contextManager->create();
                $context->setEnabled(true);
                $context->setId($context);
                $context->setName($context);

                $this->contextManager->save($context);
            }

            $instance->setContext($context);
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-6'))
                ->add('name')
                ->add('description', 'textarea', array('required' => false))
            ->end()
            ->with('Options', array('class' => 'col-md-6'))
                ->add('enabled')
                ->add('position', 'integer', array('required' => false, 'data' => 0))
                ->if_true($this->getSubject()->getParent() !== null || $this->getSubject()->getId() === null) // root category cannot have a parent
                    ->add('parent', 'sonata_category_selector', array(
                        'category'      => $this->getSubject() ?: null,
                        'model_manager' => $this->getModelManager(),
                        'class'         => $this->getClass(),
                        'required'      => true,
                        'context'       => $this->getSubject()->getContext()
                    ))
                ->end_if()
            ->end()
        ;

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $formMapper
                ->with('General')
                    ->add('media', 'sonata_type_model_list',
                        array('required' => false),
                        array(
                            'link_parameters' => array(
                                'provider' => 'sonata.media.provider.image',
                                'context'  => 'sonata_category',
                            )
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
        $options = array();
        if ($this->getPersistentParameter('hide_context') === 1) {
            $options['disabled'] = true;
        }

        $datagridMapper
            ->add('name')
            ->add('context', null, array(), null, $options)
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
            ->add('context')
            ->add('slug')
            ->add('description')
            ->add('enabled', null, array('editable' => true))
            ->add('position')
            ->add('parent')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        $parameters = array(
            'context'      => '',
            'hide_context' => (int)$this->getRequest()->get('hide_context', 0)
        );

        if ($this->getSubject()) {
            $parameters['context'] = $this->getSubject()->getContext() ? $this->getSubject()->getContext()->getId() : '';

            return $parameters;
        }

        if ($this->hasRequest()) {
            $parameters['context'] = $this->getRequest()->get('context');

            return $parameters;
        }

        return $parameters;
    }
}
