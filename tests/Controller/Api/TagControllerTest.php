<?php

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
use Sonata\ClassificationBundle\Controller\Api\TagController;
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class TagControllerTest extends TestCase
{
    public function testGetTagsAction()
    {
        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $pager = $this->createMock(Pager::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createTagController($tagManager)->getTagsAction($paramFetcher));
    }

    public function testGetTagAction()
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));

        $this->assertEquals($tag, $this->createTagController($tagManager)->getTagAction(1));
    }

    public function testGetTagNotFoundExceptionAction()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Tag (42) not found');

        $this->createTagController()->getTagAction(42);
    }

    public function testPostTagAction()
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects($this->once())->method('save')->will($this->returnValue($tag));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($tag));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->postTagAction(new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostTagInvalidAction()
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects($this->never())->method('save')->will($this->returnValue($tagManager));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->postTagAction(new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutTagAction()
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));
        $tagManager->expects($this->once())->method('save')->will($this->returnValue($tag));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($tag));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->putTagAction(1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutPostInvalidAction()
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));
        $tagManager->expects($this->never())->method('save')->will($this->returnValue($tag));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->putTagAction(1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteTagAction()
    {
        $tag = $this->createMock(TagInterface::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));
        $tagManager->expects($this->once())->method('delete');

        $view = $this->createTagController($tagManager)->deleteTagAction(1);

        $this->assertEquals(['deleted' => true], $view);
    }

    public function testDeleteTagInvalidAction()
    {
        $this->expectException(NotFoundHttpException::class);

        $tagManager = $this->createMock(TagManagerInterface::class);
        $tagManager->expects($this->once())->method('find')->will($this->returnValue(null));
        $tagManager->expects($this->never())->method('delete');

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
