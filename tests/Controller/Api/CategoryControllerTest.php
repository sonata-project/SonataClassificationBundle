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
use Sonata\ClassificationBundle\Controller\Api\CategoryController;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class CategoryControllerTest extends TestCase
{
    public function testGetCategoriesAction()
    {
        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $pager = $this->createMock(Pager::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createCategoryController($categoryManager)->getCategoriesAction($paramFetcher));
    }

    public function testGetCategoryAction()
    {
        $category = $this->createMock(CategoryInterface::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('find')->will($this->returnValue($category));

        $this->assertSame($category, $this->createCategoryController($categoryManager)->getCategoryAction(1));
    }

    public function testGetCategoryNotFoundExceptionAction()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Category (42) not found');

        $this->createCategoryController()->getCategoryAction(42);
    }

    public function testPostCategoryAction()
    {
        $category = $this->createMock(CategoryInterface::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('save')->will($this->returnValue($category));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($category));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCategoryController($categoryManager, $formFactory)->postCategoryAction(new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostCategoryInvalidAction()
    {
        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->never())->method('save')->will($this->returnValue($categoryManager));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCategoryController($categoryManager, $formFactory)->postCategoryAction(new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutCategoryAction()
    {
        $category = $this->createMock(CategoryInterface::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('find')->will($this->returnValue($category));
        $categoryManager->expects($this->once())->method('save')->will($this->returnValue($category));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($category));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCategoryController($categoryManager, $formFactory)->putCategoryAction(1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutPostInvalidAction()
    {
        $category = $this->createMock(CategoryInterface::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('find')->will($this->returnValue($category));
        $categoryManager->expects($this->never())->method('save')->will($this->returnValue($category));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCategoryController($categoryManager, $formFactory)->putCategoryAction(1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteCategoryAction()
    {
        $category = $this->createMock(CategoryInterface::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('find')->will($this->returnValue($category));
        $categoryManager->expects($this->once())->method('delete');

        $view = $this->createCategoryController($categoryManager)->deleteCategoryAction(1);

        $this->assertSame(['deleted' => true], $view);
    }

    public function testDeleteCategoryInvalidAction()
    {
        $this->expectException(NotFoundHttpException::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
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
            $categoryManager = $this->createMock(CategoryManagerInterface::class);
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock(FormFactoryInterface::class);
        }

        return new CategoryController($categoryManager, $formFactory);
    }
}
