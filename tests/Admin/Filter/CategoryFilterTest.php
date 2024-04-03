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

namespace Sonata\ClassificationBundle\Tests\Admin\Filter;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Admin\Filter\CategoryFilter;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Tests\Fixtures\Category;
use Sonata\ClassificationBundle\Tests\Fixtures\Context;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class CategoryFilterTest extends TestCase
{
    /**
     * @var Stub&CategoryManagerInterface
     */
    private CategoryManagerInterface $categoryManager;

    protected function setUp(): void
    {
        $this->categoryManager = $this->createStub(CategoryManagerInterface::class);
    }

    public function testRenderSettings(): void
    {
        $this->categoryManager->method('getAllRootCategories')->willReturn([
            $category = $this->createCategory(),
        ]);

        $filter = new CategoryFilter($this->categoryManager);
        $filter->initialize('field_name', [
            'field_options' => ['class' => 'FooBar'],
        ]);
        $options = $filter->getRenderSettings()[1];

        static::assertSame(ChoiceType::class, $options['field_type']);
        static::assertIsArray($options['field_options']);
        static::assertCount(1, $options['field_options']['choices']);
    }

    public function testRenderSettingsWithContext(): void
    {
        $this->categoryManager->method('getRootCategoriesForContext')->willReturn([
            $category = $this->createCategory(),
        ]);

        $filter = new CategoryFilter($this->categoryManager);
        $filter->initialize('field_name', [
            'context' => new Context(),
            'field_options' => [
                'class' => 'FooBar',
            ],
        ]);
        $options = $filter->getRenderSettings()[1];

        static::assertSame(ChoiceType::class, $options['field_type']);
        static::assertIsArray($options['field_options']);
        static::assertCount(1, $options['field_options']['choices']);
    }

    private function createCategory(): Category
    {
        $category = new Category();
        $category->setContext(new Context());

        return $category;
    }
}
