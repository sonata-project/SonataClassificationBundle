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
    public function testGetContextsAction(): void
    {
        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects(static::once())->method('all')->willReturn([]);

        $pager = $this->createMock(Pager::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects(static::once())->method('getPager')->willReturn($pager);

        static::assertSame($pager, $this->createContextController($contextManager)->getContextsAction($paramFetcher));
    }

    public function testGetContextAction(): void
    {
        $context = $this->createMock(ContextInterface::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects(static::once())->method('find')->willReturn($context);

        static::assertSame($context, $this->createContextController($contextManager)->getContextAction(1));
    }

    public function testGetContextNotFoundExceptionAction(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Context (42) not found');

        $this->createContextController()->getContextAction(42);
    }

    public function testPostContextAction(): void
    {
        $context = $this->createMock(ContextInterface::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects(static::once())->method('save')->willReturn($context);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($context);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createContextController($contextManager, $formFactory)->postContextAction(new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPostContextInvalidAction(): void
    {
        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects(static::never())->method('save')->willReturn($contextManager);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createContextController($contextManager, $formFactory)->postContextAction(new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutContextAction(): void
    {
        $context = $this->createMock(ContextInterface::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects(static::once())->method('find')->willReturn($context);
        $contextManager->expects(static::once())->method('save')->willReturn($context);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($context);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createContextController($contextManager, $formFactory)->putContextAction(1, new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPutPostInvalidAction(): void
    {
        $context = $this->createMock(ContextInterface::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects(static::once())->method('find')->willReturn($context);
        $contextManager->expects(static::never())->method('save')->willReturn($context);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createContextController($contextManager, $formFactory)->putContextAction(1, new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteContextAction(): void
    {
        $context = $this->createMock(ContextInterface::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects(static::once())->method('find')->willReturn($context);
        $contextManager->expects(static::once())->method('delete');

        $view = $this->createContextController($contextManager)->deleteContextAction(1);

        static::assertSame(['deleted' => true], $view);
    }

    public function testDeleteContextInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects(static::once())->method('find')->willReturn(null);
        $contextManager->expects(static::never())->method('delete');

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
