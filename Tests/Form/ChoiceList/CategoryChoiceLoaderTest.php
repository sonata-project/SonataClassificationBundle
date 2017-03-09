<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Tests\Form\ChoiceList;

use Sonata\ClassificationBundle\Form\ChoiceList\CategoryChoiceLoader;
use Sonata\ClassificationBundle\Tests\Helpers\PHPUnit_Framework_TestCase;

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
class CategoryChoiceLoaderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!interface_exists('Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface')) {
            $this->markTestSkipped('Test only available for >= SF3.0');
        }
    }

    public function testLoadChoiceList()
    {
        $choices = array(
            1 => 'foo',
            2 => 'bar',
        );

        $categoryLoader = new CategoryChoiceLoader(array_flip($choices));

        $this->assertSame($choices, $categoryLoader->loadChoiceList()->getOriginalKeys());
    }

    public function testLoadChoicesForValues()
    {
        $choices = array(
            1 => 'foo',
            2 => 'bar',
        );

        $categoryLoader = new CategoryChoiceLoader(array_flip($choices));

        $this->assertSame(array_keys($choices), $categoryLoader->loadChoicesForValues(array(1, 2, 3)));
    }

    public function testLoadValuesForChoices()
    {
        $choices = array(
            1 => 'foo',
            2 => 'bar',
        );

        $categoryLoader = new CategoryChoiceLoader(array_flip($choices));

        //due to string typecast of values inside of ArrayChoiceList
        $expectedChoices = array(
            'foo' => '1',
            'bar' => '2',
        );

        $choices['3'] = 'extra';

        $this->assertSame($expectedChoices, $categoryLoader->loadValuesForChoices(array_flip($choices)));
    }
}
