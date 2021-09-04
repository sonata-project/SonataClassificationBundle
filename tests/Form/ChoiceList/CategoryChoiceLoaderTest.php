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

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
class CategoryChoiceLoaderTest extends TestCase
{
    public function testLoadChoiceList(): void
    {
        $choices = [
            1 => 'foo',
            2 => 'bar',
        ];

        $categoryLoader = new CategoryChoiceLoader(array_flip($choices));

        static::assertSame($choices, $categoryLoader->loadChoiceList()->getOriginalKeys());
    }

    public function testLoadChoicesForValues(): void
    {
        $choices = [
            1 => 'foo',
            2 => 'bar',
        ];

        $categoryLoader = new CategoryChoiceLoader(array_flip($choices));

        static::assertSame(array_keys($choices), $categoryLoader->loadChoicesForValues([1, 2, 3]));
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

        static::assertSame($expectedChoices, $categoryLoader->loadValuesForChoices(array_flip($choices)));
    }
}
