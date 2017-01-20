<?php

namespace Sonata\ClassificationBundle\Tests\Form\Type;

use Sonata\ClassificationBundle\Form\Type\CategorySelectorType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorySelectorTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigureOptions()
    {
        $manager = $this->createMock('Sonata\CoreBundle\Model\ManagerInterface');
        $categorySelectorType = new CategorySelectorType($manager);
        $categorySelectorType->configureOptions(new OptionsResolver());
    }
}
