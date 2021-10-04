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
use Sonata\ClassificationBundle\Controller\Api\TagController;
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * NEXT_MAJOR: Remove this class.
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 *
 * @group legacy
 */
class TagControllerTest extends TestCase
{
    public function testGetTagsAction(): void
    {
        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->method('get')->willReturnMap([
            ['page', null, 1],
            ['count', null, 25],
        ]);
        $paramFetcher->expects(static::once())->method('all')->willReturn([]);

        $pager = $this->createMock(PagerInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects(static::once())->method('getPager')->willReturn($pager);

        static::assertSame($pager, $this->createTagController($tagManager)->getTagsAction($paramFetcher));
    }

    public function testGetTagAction(): void
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects(static::once())->method('find')->willReturn($tag);

        static::assertSame($tag, $this->createTagController($tagManager)->getTagAction(1));
    }

    public function testGetTagNotFoundExceptionAction(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Tag (42) not found');

        $this->createTagController()->getTagAction(42);
    }

    public function testPostTagAction(): void
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects(static::once())->method('save')->willReturn($tag);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($tag);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createTagController($tagManager, $formFactory)->postTagAction(new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPostTagInvalidAction(): void
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects(static::never())->method('save')->willReturn($tagManager);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createTagController($tagManager, $formFactory)->postTagAction(new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutTagAction(): void
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects(static::once())->method('find')->willReturn($tag);
        $tagManager->expects(static::once())->method('save')->willReturn($tag);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($tag);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createTagController($tagManager, $formFactory)->putTagAction(1, new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPutPostInvalidAction(): void
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects(static::once())->method('find')->willReturn($tag);
        $tagManager->expects(static::never())->method('save')->willReturn($tag);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createTagController($tagManager, $formFactory)->putTagAction(1, new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteTagAction(): void
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects(static::once())->method('find')->willReturn($tag);
        $tagManager->expects(static::once())->method('delete');

        $view = $this->createTagController($tagManager)->deleteTagAction(1);

        static::assertSame(['deleted' => true], $view->getData());
    }

    public function testDeleteTagInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects(static::once())->method('find')->willReturn(null);
        $tagManager->expects(static::never())->method('delete');

        $this->createTagController($tagManager)->deleteTagAction(1);
    }

    /**
     * Creates a new TagController.
     *
     * @param null $tagManager
     * @param null $formFactory
     *
     * @return TagController
     */
    protected function createTagController($tagManager = null, $formFactory = null)
    {
        if (null === $tagManager) {
            $tagManager = $this->createMock(TagManagerInterface::class);
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock(FormFactoryInterface::class);
        }

        return new TagController($tagManager, $formFactory);
    }
}
