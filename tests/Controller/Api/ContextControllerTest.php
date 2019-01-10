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

namespace Sonata\ClassificationBundle\Tests\Controller\Api;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Datagrid\Pager;
use Sonata\ClassificationBundle\Controller\Api\ContextController;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Thomas Rabaix <thomas.rabaix@gmail.com>
 */
class ContextControllerTest extends TestCase
{
    public function testGetContextsAction()
    {
        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $pager = $this->createMock(Pager::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createContextController($contextManager)->getContextsAction($paramFetcher));
    }

    public function testGetContextAction()
    {
        $context = $this->createMock(ContextInterface::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects($this->once())->method('find')->will($this->returnValue($context));

        $this->assertEquals($context, $this->createContextController($contextManager)->getContextAction(1));
    }

    public function testGetContextNotFoundExceptionAction()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Context (42) not found');

        $this->createContextController()->getContextAction(42);
    }

    public function testPostContextAction()
    {
        $context = $this->createMock(ContextInterface::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects($this->once())->method('save')->will($this->returnValue($context));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($context));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createContextController($contextManager, $formFactory)->postContextAction(new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostContextInvalidAction()
    {
        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects($this->never())->method('save')->will($this->returnValue($contextManager));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createContextController($contextManager, $formFactory)->postContextAction(new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutContextAction()
    {
        $context = $this->createMock(ContextInterface::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects($this->once())->method('find')->will($this->returnValue($context));
        $contextManager->expects($this->once())->method('save')->will($this->returnValue($context));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($context));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createContextController($contextManager, $formFactory)->putContextAction(1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutPostInvalidAction()
    {
        $context = $this->createMock(ContextInterface::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects($this->once())->method('find')->will($this->returnValue($context));
        $contextManager->expects($this->never())->method('save')->will($this->returnValue($context));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createContextController($contextManager, $formFactory)->putContextAction(1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteContextAction()
    {
        $context = $this->createMock(ContextInterface::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects($this->once())->method('find')->will($this->returnValue($context));
        $contextManager->expects($this->once())->method('delete');

        $view = $this->createContextController($contextManager)->deleteContextAction(1);

        $this->assertEquals(['deleted' => true], $view);
    }

    public function testDeleteContextInvalidAction()
    {
        $this->expectException(NotFoundHttpException::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
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
            $contextManager = $this->createMock(ContextManagerInterface::class);
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock(FormFactoryInterface::class);
        }

        return new ContextController($contextManager, $formFactory);
    }
}
