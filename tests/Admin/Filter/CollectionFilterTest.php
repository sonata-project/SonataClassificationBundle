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
use Sonata\ClassificationBundle\Admin\Filter\CollectionFilter;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class CollectionFilterTest extends TestCase
{
    /**
     * @var Stub&CollectionManagerInterface
     */
    private CollectionManagerInterface $collectionManager;

    protected function setUp(): void
    {
        $this->collectionManager = static::createStub(CollectionManagerInterface::class);
    }

    public function testRenderSettings(): void
    {
        $this->collectionManager->method('findAll')->willReturn([]);

        $filter = new CollectionFilter($this->collectionManager);
        $filter->initialize('field_name', [
            'field_options' => ['class' => 'FooBar'],
        ]);
        $options = $filter->getRenderSettings()[1];

        static::assertSame(ChoiceType::class, $options['field_type']);
        static::assertIsArray($options['field_options']);
        static::assertSame([], $options['field_options']['choices']);
    }
}
