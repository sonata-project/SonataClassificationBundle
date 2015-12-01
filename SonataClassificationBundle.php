<?php

namespace Sonata\ClassificationBundle;

use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataClassificationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $this->registerFormMapping();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerFormMapping();
    }

    /**
     * Register form mapping information
     */
    public function registerFormMapping()
    {
        FormHelper::registerFormTypeMapping(array(
            'sonata_classification_api_form_category'   => 'Sonata\CoreBundle\Form\Type\DoctrineORMSerializationType',
            'sonata_classification_api_form_collection' => 'Sonata\CoreBundle\Form\Type\DoctrineORMSerializationType',
            'sonata_classification_api_form_tag'        => 'Sonata\CoreBundle\Form\Type\DoctrineORMSerializationType',
            'sonata_classification_api_form_context'    => 'Sonata\CoreBundle\Form\Type\DoctrineORMSerializationType',
            'sonata_category_selector'                  => 'Sonata\ClassificationBundle\Form\Type\CategorySelectorType',
        ));
    }
}
