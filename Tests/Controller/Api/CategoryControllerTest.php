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

use Sonata\ClassificationBundle\Controller\Api\CategoryController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryControllerTest.
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class CategoryControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCategoriesAction()
    {
        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $pager = $this->getMockBuilder('Sonata\AdminBundle\Datagrid\Pager')->disableOriginalConstructor()->getMock();

        $categoryManager = $this->getMock('Sonata\ClassificationBundle\Model\CategoryManagerInterface');
        $categoryManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createCategoryController($categoryManager)->getCategoriesAction($paramFetcher));
    }

    public function testGetCategoryAction()
    {
        $category = $this->getMock('Sonata\ClassificationBundle\Model\CategoryInterface');

        $categoryManager = $this->getMock('Sonata\ClassificationBundle\Model\CategoryManagerInterface');
        $categoryManager->expects($this->once())->method('find')->will($this->returnValue($category));

        $this->assertEquals($category, $this->createCategoryController($categoryManager)->getCategoryAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Category (42) not found
     */
    public function testGetCategoryNotFoundExceptionAction()
    {
        $this->createCategoryController()->getCategoryAction(42);
    }

    public function testPostCategoryAction()
    {
        $category = $this->getMock('Sonata\ClassificationBundle\Model\CategoryInterface');

        $categoryManager = $this->getMock('Sonata\ClassificationBundle\Model\CategoryManagerInterface');
        $categoryManager->expects($this->once())->method('save')->will($this->returnValue($category));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($category));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCategoryController($categoryManager, $formFactory)->postCategoryAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostCategoryInvalidAction()
    {
        $categoryManager = $this->getMock('Sonata\ClassificationBundle\Model\CategoryManagerInterface');
        $categoryManager->expects($this->never())->method('save')->will($this->returnValue($categoryManager));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCategoryController($categoryManager, $formFactory)->postCategoryAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutCategoryAction()
    {
        $category = $this->getMock('Sonata\ClassificationBundle\Model\CategoryInterface');

        $categoryManager = $this->getMock('Sonata\ClassificationBundle\Model\CategoryManagerInterface');
        $categoryManager->expects($this->once())->method('find')->will($this->returnValue($category));
        $categoryManager->expects($this->once())->method('save')->will($this->returnValue($category));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($category));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCategoryController($categoryManager, $formFactory)->putCategoryAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutPostInvalidAction()
    {
        $category = $this->getMock('Sonata\ClassificationBundle\Model\CategoryInterface');

        $categoryManager = $this->getMock('Sonata\ClassificationBundle\Model\CategoryManagerInterface');
        $categoryManager->expects($this->once())->method('find')->will($this->returnValue($category));
        $categoryManager->expects($this->never())->method('save')->will($this->returnValue($category));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCategoryController($categoryManager, $formFactory)->putCategoryAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteCategoryAction()
    {
        $category = $this->getMock('Sonata\ClassificationBundle\Model\CategoryInterface');

        $categoryManager = $this->getMock('Sonata\ClassificationBundle\Model\CategoryManagerInterface');
        $categoryManager->expects($this->once())->method('find')->will($this->returnValue($category));
        $categoryManager->expects($this->once())->method('delete');

        $view = $this->createCategoryController($categoryManager)->deleteCategoryAction(1);

        $this->assertEquals(array('deleted' => true), $view);
    }

    public function testDeleteCategoryInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $categoryManager = $this->getMock('Sonata\ClassificationBundle\Model\CategoryManagerInterface');
        $categoryManager->expects($this->once())->method('find')->will($this->returnValue(null));
        $categoryManager->expects($this->never())->method('delete');

        $this->createCategoryController($categoryManager)->deleteCategoryAction(1);
    }

    /**
     * Creates a new CategoryController.
     *
     * @param null $categoryManager
     * @param null $formFactory
     *
     * @return CategoryController
     */
    protected function createCategoryController($categoryManager = null, $formFactory = null)
    {
        if (null === $categoryManager) {
            $categoryManager = $this->getMock('Sonata\ClassificationBundle\Model\CategoryManagerInterface');
        }
        if (null === $formFactory) {
            $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new CategoryController($categoryManager, $formFactory);
    }
}
