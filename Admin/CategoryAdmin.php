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

class CategoryAdmin extends Admin
{
    protected $formOptions = array(
        'cascade_validation' => true
    );

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, array('required' => false))
            ->add('name')
            ->add('description', 'textarea', array('required' => false))
            ->add('position', 'integer', array('required' => false, 'data' => 0))
            ->add('parent', 'sonata_category_selector', array(
                'category'      => $this->getSubject() ?: null,
                'model_manager' => $this->getModelManager(),
                'class'         => $this->getClass(),
                'required'      => false
            ))
        ;

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $formMapper->add('media', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'sonata_category',
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('enabled')
            ->add('parent')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('slug')
            ->add('description')
            ->add('enabled', null, array('editable' => true))
            ->add('position')
            ->add('parent')
        ;
    }
}
