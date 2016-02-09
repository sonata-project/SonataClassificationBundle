<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Tests\Controller\Api;

use Sonata\ClassificationBundle\Controller\Api\ContextController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContextControllerTest.
 *
 * @author Thomas Rabaix <thomas.rabaix@gmail.com>
 */
class ContextControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContextsAction()
    {
        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $pager = $this->getMockBuilder('Sonata\AdminBundle\Datagrid\Pager')->disableOriginalConstructor()->getMock();

        $contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $contextManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createContextController($contextManager)->getContextsAction($paramFetcher));
    }

    public function testGetContextAction()
    {
        $context = $this->getMock('Sonata\ClassificationBundle\Model\ContextInterface');

        $contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $contextManager->expects($this->once())->method('find')->will($this->returnValue($context));

        $this->assertEquals($context, $this->createContextController($contextManager)->getContextAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Context (42) not found
     */
    public function testGetContextNotFoundExceptionAction()
    {
        $this->createContextController()->getContextAction(42);
    }

    public function testPostContextAction()
    {
        $context = $this->getMock('Sonata\ClassificationBundle\Model\ContextInterface');

        $contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $contextManager->expects($this->once())->method('save')->will($this->returnValue($context));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($context));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createContextController($contextManager, $formFactory)->postContextAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostContextInvalidAction()
    {
        $contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $contextManager->expects($this->never())->method('save')->will($this->returnValue($contextManager));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createContextController($contextManager, $formFactory)->postContextAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutContextAction()
    {
        $context = $this->getMock('Sonata\ClassificationBundle\Model\ContextInterface');

        $contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $contextManager->expects($this->once())->method('find')->will($this->returnValue($context));
        $contextManager->expects($this->once())->method('save')->will($this->returnValue($context));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($context));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createContextController($contextManager, $formFactory)->putContextAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutPostInvalidAction()
    {
        $context = $this->getMock('Sonata\ClassificationBundle\Model\ContextInterface');

        $contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $contextManager->expects($this->once())->method('find')->will($this->returnValue($context));
        $contextManager->expects($this->never())->method('save')->will($this->returnValue($context));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createContextController($contextManager, $formFactory)->putContextAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteContextAction()
    {
        $context = $this->getMock('Sonata\ClassificationBundle\Model\ContextInterface');

        $contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $contextManager->expects($this->once())->method('find')->will($this->returnValue($context));
        $contextManager->expects($this->once())->method('delete');

        $view = $this->createContextController($contextManager)->deleteContextAction(1);

        $this->assertEquals(array('deleted' => true), $view);
    }

    public function testDeleteContextInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        $contextManager->expects($this->once())->method('find')->will($this->returnValue(null));
        $contextManager->expects($this->never())->method('delete');

        $this->createContextController($contextManager)->deleteContextAction(1);
    }

    /**
     * Creates a new ContextController.
     *
     * @param null $contextManager
     * @param null $formFactory
     *
     * @return ContextController
     */
    protected function createContextController($contextManager = null, $formFactory = null)
    {
        if (null === $contextManager) {
            $contextManager = $this->getMock('Sonata\ClassificationBundle\Model\ContextManagerInterface');
        }
        if (null === $formFactory) {
            $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new ContextController($contextManager, $formFactory);
    }
}
