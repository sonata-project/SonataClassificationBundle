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

namespace Sonata\ClassificationBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Model\Context;

/**
 * @author Dariusz Markowicz <dmarkowicz77@gmail.com>
 */
class ContextTest extends TestCase
{
    public function testSetterGetter(): void
    {
        $time = new \DateTime();

        /** @var Context $context */
        $context = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Model\Context');
        // id is an int in ContextInterface and Context but used as string in implementation
        // see ContextInterface::DEFAULT_CONTEXT
        $context->setId(2);
        $context->setName('Hello World');
        $context->setCreatedAt($time);
        $context->setUpdatedAt($time);
        $context->setEnabled(true);

        $this->assertEquals('Hello World', $context->getName());
        $this->assertEquals('Hello World', $context->__toString());
        $this->assertEquals($time, $context->getCreatedAt());
        $this->assertEquals($time, $context->getUpdatedAt());
        $this->assertTrue($context->getEnabled());

        $context->setName('');
        $this->assertEquals('n/a', $context->__toString());
    }

    public function testPreUpdate(): void
    {
        /** @var Context $context */
        $context = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Model\Context');
        $context->preUpdate();

        $this->assertInstanceOf('\DateTime', $context->getUpdatedAt());
    }
}
