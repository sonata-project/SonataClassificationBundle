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

namespace Sonata\AdminBundle\Tests\Form\ChoiceList;

use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Form\ChoiceList\CategoryChoiceLoader;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
class CategoryChoiceLoaderTest extends TestCase
{
    protected function setUp(): void
    {
        if (!interface_exists(ChoiceLoaderInterface::class)) {
            $this->markTestSkipped('Test only available for >= SF3.0');
        }
    }

    public function testLoadChoiceList(): void
    {
        $choices = [
            1 => 'foo',
            2 => 'bar',
        ];

        $categoryLoader = new CategoryChoiceLoader(array_flip($choices));

        $this->assertSame($choices, $categoryLoader->loadChoiceList()->getOriginalKeys());
    }

    public function testLoadChoicesForValues(): void
    {
        $choices = [
            1 => 'foo',
            2 => 'bar',
        ];

        $categoryLoader = new CategoryChoiceLoader(array_flip($choices));

        $this->assertSame(array_keys($choices), $categoryLoader->loadChoicesForValues([1, 2, 3]));
    }

    public function testLoadValuesForChoices(): void
    {
        $choices = [
            1 => 'foo',
            2 => 'bar',
        ];

        $categoryLoader = new CategoryChoiceLoader(array_flip($choices));

        //due to string typecast of values inside of ArrayChoiceList
        $expectedChoices = [
            'foo' => '1',
            'bar' => '2',
        ];

        $choices['3'] = 'extra';

        $this->assertSame($expectedChoices, $categoryLoader->loadValuesForChoices(array_flip($choices)));
    }
}
