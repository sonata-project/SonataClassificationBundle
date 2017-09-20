<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Tests\Form\Type;

use Sonata\ClassificationBundle\Form\Type\CategorySelectorType;
use Sonata\ClassificationBundle\Tests\Helpers\PHPUnit_Framework_TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
class CategorySelectorTypeTest extends PHPUnit_Framework_TestCase
{
    public function testConfigureOptions()
    {
        $manager = $this->createMock('Sonata\CoreBundle\Model\ManagerInterface');
        $categorySelectorType = new CategorySelectorType($manager);
        $optionsResolver = new OptionsResolver();
        $categorySelectorType->configureOptions($optionsResolver);
        //unable to get defined options on SF2.3
        if (!method_exists($optionsResolver, 'getDefinedOptions')) {
            return;
        }
        $definedOptions = $optionsResolver->getDefinedOptions();
        $this->assertContains('category', $definedOptions);
        $this->assertContains('context', $definedOptions);
        if (interface_exists('Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface')) {
            $this->assertContains('choice_loader', $definedOptions);
            $this->assertNotContains('choice_list', $definedOptions);
        } else {
            $this->assertContains('choice_list', $definedOptions);
            $this->assertNotContains('choice_loader', $definedOptions);
        }
    }
}
