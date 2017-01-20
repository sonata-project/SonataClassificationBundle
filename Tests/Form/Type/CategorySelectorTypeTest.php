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
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorySelectorTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigureOptions()
    {
        $manager = $this->getMock('Sonata\CoreBundle\Model\ManagerInterface');
        $categorySelectorType = new CategorySelectorType($manager);
        $categorySelectorType->configureOptions(new OptionsResolver());
    }
}
