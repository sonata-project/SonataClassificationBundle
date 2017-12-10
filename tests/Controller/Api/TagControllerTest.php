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

use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Controller\Api\TagController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class TagControllerTest extends TestCase
{
    public function testGetTagsAction(): void
    {
        $paramFetcher = $this->createMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $pager = $this->getMockBuilder('Sonata\AdminBundle\Datagrid\Pager')->disableOriginalConstructor()->getMock();

        $tagManager = $this->createMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createTagController($tagManager)->getTagsAction($paramFetcher));
    }

    public function testGetTagAction(): void
    {
        $tag = $this->createMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->createMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));

        $this->assertEquals($tag, $this->createTagController($tagManager)->getTagAction(1));
    }

    public function testGetTagNotFoundExceptionAction(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('Tag (42) not found');

        $this->createTagController()->getTagAction(42);
    }

    public function testPostTagAction(): void
    {
        $tag = $this->createMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->createMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('save')->will($this->returnValue($tag));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($tag));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->postTagAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostTagInvalidAction(): void
    {
        $tag = $this->createMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->createMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->never())->method('save')->will($this->returnValue($tagManager));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->postTagAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutTagAction(): void
    {
        $tag = $this->createMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->createMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));
        $tagManager->expects($this->once())->method('save')->will($this->returnValue($tag));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($tag));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->putTagAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutPostInvalidAction(): void
    {
        $tag = $this->createMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->createMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));
        $tagManager->expects($this->never())->method('save')->will($this->returnValue($tag));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->putTagAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteTagAction(): void
    {
        $tag = $this->createMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->createMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));
        $tagManager->expects($this->once())->method('delete');

        $view = $this->createTagController($tagManager)->deleteTagAction(1);

        $this->assertEquals(['deleted' => true], $view);
    }

    public function testDeleteTagInvalidAction(): void
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $tagManager = $this->createMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
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
            $tagManager = $this->createMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new TagController($tagManager, $formFactory);
    }
}
