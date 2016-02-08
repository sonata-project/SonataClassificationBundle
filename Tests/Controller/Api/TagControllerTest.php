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

use Sonata\ClassificationBundle\Controller\Api\TagController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TagControllerTest.
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class TagControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTagsAction()
    {
        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $pager = $this->getMockBuilder('Sonata\AdminBundle\Datagrid\Pager')->disableOriginalConstructor()->getMock();

        $tagManager = $this->getMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createTagController($tagManager)->getTagsAction($paramFetcher));
    }

    public function testGetTagAction()
    {
        $tag = $this->getMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->getMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));

        $this->assertEquals($tag, $this->createTagController($tagManager)->getTagAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Tag (42) not found
     */
    public function testGetTagNotFoundExceptionAction()
    {
        $this->createTagController()->getTagAction(42);
    }

    public function testPostTagAction()
    {
        $tag = $this->getMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->getMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('save')->will($this->returnValue($tag));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($tag));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->postTagAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostTagInvalidAction()
    {
        $tag = $this->getMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->getMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->never())->method('save')->will($this->returnValue($tagManager));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->postTagAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutTagAction()
    {
        $tag = $this->getMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->getMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));
        $tagManager->expects($this->once())->method('save')->will($this->returnValue($tag));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($tag));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->putTagAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutPostInvalidAction()
    {
        $tag = $this->getMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->getMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));
        $tagManager->expects($this->never())->method('save')->will($this->returnValue($tag));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createTagController($tagManager, $formFactory)->putTagAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteTagAction()
    {
        $tag = $this->getMock('Sonata\ClassificationBundle\Model\TagInterface');

        $tagManager = $this->getMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        $tagManager->expects($this->once())->method('find')->will($this->returnValue($tag));
        $tagManager->expects($this->once())->method('delete');

        $view = $this->createTagController($tagManager)->deleteTagAction(1);

        $this->assertEquals(array('deleted' => true), $view);
    }

    public function testDeleteTagInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $tagManager = $this->getMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
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
            $tagManager = $this->getMock('Sonata\ClassificationBundle\Model\TagManagerInterface');
        }
        if (null === $formFactory) {
            $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new TagController($tagManager, $formFactory);
    }
}
