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
use Sonata\Doctrine\Model\ManagerInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
class CategorySelectorTypeTest extends TestCase
{
    public function testConfigureOptions()
    {
        $manager = $this->createMock(ManagerInterface::class);
        $categorySelectorType = new CategorySelectorType($manager);
        $optionsResolver = new OptionsResolver();
        $categorySelectorType->configureOptions($optionsResolver);

        $definedOptions = $optionsResolver->getDefinedOptions();
        $this->assertContains('category', $definedOptions);
        $this->assertContains('context', $definedOptions);
        if (interface_exists(ChoiceLoaderInterface::class)) {
            $this->assertContains('choice_loader', $definedOptions);
            $this->assertNotContains('choice_list', $definedOptions);
        } else {
            $this->assertContains('choice_list', $definedOptions);
            $this->assertNotContains('choice_loader', $definedOptions);
        }
    }
}
