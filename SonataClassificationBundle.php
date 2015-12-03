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
     * Register form mapping information.
     */
    public function registerFormMapping()
    {
        FormHelper::registerFormTypeMapping(array(
            'sonata_classification_api_form_category'   => 'Sonata\ClassificationBundle\Form\Type\ApiCategoryType',
            'sonata_classification_api_form_collection' => 'Sonata\ClassificationBundle\Form\Type\ApiCollectionType',
            'sonata_classification_api_form_tag'        => 'Sonata\ClassificationBundle\Form\Type\ApiTagType',
            'sonata_classification_api_form_context'    => 'Sonata\ClassificationBundle\Form\Type\ApiContextType',
            'sonata_category_selector'                  => 'Sonata\ClassificationBundle\Form\Type\CategorySelectorType',
        ));
    }
}
