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

namespace Sonata\ClassificationBundle\Tests\Admin;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminExtensionInterface;
use Sonata\ClassificationBundle\Admin\ContextAdmin;
use Sonata\ClassificationBundle\Admin\ContextAwareAdmin;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;

class AdminTest extends TestCase
{
    /**
     * @var ContextManagerInterface
     */
    private $contextManager;

    public function setUp(): void
    {
        $this->contextManager = $this->createMock(ContextManagerInterface::class);
    }

    public function testAbstractAdminChildren(): void
    {
        $contextAwareAdmin = $this->createMock(ContextAwareAdmin::class);
        $this->assertInstanceOf(AbstractAdmin::class, $contextAwareAdmin);
        $contextAdmin = $this->createMock(ContextAdmin::class);
        $this->assertInstanceOf(AbstractAdmin::class, $contextAdmin);
    }

    public function testGetPersistentParametersWithNoExtension(): void
    {
        $expected = [
            'context' => '',
            'hide_context' => 0,
        ];

        $admin = $this->getMockForAbstractClass(ContextAwareAdmin::class, [
            'admin.my_code', 'My\Class', 'MyBundle:ClassAdmin', $this->contextManager,
        ]);

        $this->assertSame($expected, $admin->getPersistentParameters());
    }

    public function testGetPersistentParametersWithInvalidExtension(): void
    {
        $this->expectException(\RuntimeException::class);

        $admin = $this->getMockForAbstractClass(ContextAwareAdmin::class, [
            'admin.my_code', 'My\Class', 'MyBundle:ClassAdmin', $this->contextManager,
        ]);

        $extension = $this->createMock(AdminExtensionInterface::class);
        $extension->expects($this->once())->method('getPersistentParameters')->will($this->returnValue(null));

        $admin->addExtension($extension);

        $admin->getPersistentParameters();
    }

    public function testGetPersistentParametersWithValidExtension(): void
    {
        $expected = [
            'tl' => 'de',
            'abc' => 123,
            'context' => '',
            'hide_context' => 0,
        ];

        $extensionParams = [
            'tl' => 'de',
            'abc' => 123,
        ];

        $admin = $this->getMockForAbstractClass(ContextAwareAdmin::class, [
            'admin.my_code', 'My\Class', 'MyBundle:ClassAdmin', $this->contextManager,
        ]);

        $extension = $this->createMock(AdminExtensionInterface::class);
        $extension->expects($this->once())->method('getPersistentParameters')->will($this->returnValue($extensionParams));

        $admin->addExtension($extension);

        $this->assertSame($expected, $admin->getPersistentParameters());
    }
}
