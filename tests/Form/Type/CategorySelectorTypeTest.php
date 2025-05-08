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

namespace Sonata\ClassificationBundle\Tests\Form\Type;

use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Form\Type\CategorySelectorType;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
final class CategorySelectorTypeTest extends TestCase
{
    public function testConfigureOptions(): void
    {
        $manager = static::createStub(CategoryManagerInterface::class);
        $categorySelectorType = new CategorySelectorType($manager);
        $optionsResolver = new OptionsResolver();
        $categorySelectorType->configureOptions($optionsResolver);

        $definedOptions = $optionsResolver->getDefinedOptions();
        static::assertContains('category', $definedOptions);
        static::assertContains('context', $definedOptions);
        if (interface_exists(ChoiceLoaderInterface::class)) {
            static::assertContains('choice_loader', $definedOptions);
            static::assertNotContains('choice_list', $definedOptions);
        } else {
            static::assertContains('choice_list', $definedOptions);
            static::assertNotContains('choice_loader', $definedOptions);
        }
    }
}
