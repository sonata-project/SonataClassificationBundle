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

namespace Sonata\ClassificationBundle;

use Sonata\ClassificationBundle\Form\Type\ApiCategoryType;
use Sonata\ClassificationBundle\Form\Type\ApiCollectionType;
use Sonata\ClassificationBundle\Form\Type\ApiContextType;
use Sonata\ClassificationBundle\Form\Type\ApiTagType;
use Sonata\ClassificationBundle\Form\Type\CategorySelectorType;
use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataClassificationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $this->registerFormMapping();
    }

    public function boot()
    {
        $this->registerFormMapping();
    }

    /**
     * Register form mapping information.
     *
     * NEXT_MAJOR: remove this method
     */
    public function registerFormMapping()
    {
        if (class_exists(FormHelper::class)) {
            FormHelper::registerFormTypeMapping([
                'sonata_classification_api_form_category' => ApiCategoryType::class,
                'sonata_classification_api_form_collection' => ApiCollectionType::class,
                'sonata_classification_api_form_tag' => ApiTagType::class,
                'sonata_classification_api_form_context' => ApiContextType::class,
                'sonata_category_selector' => CategorySelectorType::class,
            ]);
        }
    }
}
