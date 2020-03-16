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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Admin\Filter\CollectionFilter;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CollectionFilterTest extends TestCase
{
    /**
     * @var MockObject&CollectionManagerInterface
     */
    private $collectionManager;

    protected function setUp(): void
    {
        $this->collectionManager = $this->createStub(CollectionManagerInterface::class);
    }

    public function testRenderSettings(): void
    {
        $this->collectionManager->method('findAll')->willReturn([]);

        $filter = new CollectionFilter($this->collectionManager);
        $filter->initialize('field_name', [
            'field_options' => ['class' => 'FooBar'],
            ]);
        $options = $filter->getRenderSettings()[1];

        $this->assertSame(ChoiceType::class, $options['field_type']);
        $this->assertSame([], $options['field_options']['choices']);
    }
}
