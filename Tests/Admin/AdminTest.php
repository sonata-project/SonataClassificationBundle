<?php

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
use Sonata\ClassificationBundle\Model\ContextManagerInterface;

class AdminTest extends TestCase
{
    /**
     * @var ContextManagerInterface
     */
    private $contextManager;

    public function setUp()
    {
        $this->contextManager = $this->createMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
    }

    public function testAbstractAdminChildren()
    {
        $contextAwareAdmin = $this->getMockBuilder('Sonata\ClassificationBundle\Admin\ContextAwareAdmin')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertInstanceOf('Sonata\AdminBundle\Admin\AbstractAdmin', $contextAwareAdmin);
        $contextAdmin = $this->getMockBuilder('Sonata\ClassificationBundle\Admin\ContextAdmin')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertInstanceOf('Sonata\AdminBundle\Admin\AbstractAdmin', $contextAdmin);
    }

    public function testGetPersistentParametersWithNoExtension()
    {
        $expected = [
            'context' => '',
            'hide_context' => 0,
        ];

        $admin = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Admin\ContextAwareAdmin', [
            'admin.my_code', 'My\Class', 'MyBundle:ClassAdmin', $this->contextManager,
        ]);

        $this->assertSame($expected, $admin->getPersistentParameters());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetPersistentParametersWithInvalidExtension()
    {
        $admin = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Admin\ContextAwareAdmin', [
            'admin.my_code', 'My\Class', 'MyBundle:ClassAdmin', $this->contextManager,
        ]);

        $extension = $this->createMock('Sonata\AdminBundle\Admin\AdminExtensionInterface');
        $extension->expects($this->once())->method('getPersistentParameters')->will($this->returnValue(null));

        $admin->addExtension($extension);

        $admin->getPersistentParameters();
    }

    public function testGetPersistentParametersWithValidExtension()
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

        $admin = $this->getMockForAbstractClass('Sonata\ClassificationBundle\Admin\ContextAwareAdmin', [
            'admin.my_code', 'My\Class', 'MyBundle:ClassAdmin', $this->contextManager,
        ]);

        $extension = $this->createMock('Sonata\AdminBundle\Admin\AdminExtensionInterface');
        $extension->expects($this->once())->method('getPersistentParameters')->will($this->returnValue($extensionParams));

        $admin->addExtension($extension);

        $this->assertSame($expected, $admin->getPersistentParameters());
    }
}
