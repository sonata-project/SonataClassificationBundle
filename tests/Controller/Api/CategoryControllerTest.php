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
    public function testGetCategoriesAction(): void
    {
        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects($this->once())->method('all')->willReturn([]);

        $pager = $this->createMock(Pager::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('getPager')->willReturn($pager);

        $this->assertSame($pager, $this->createCategoryController($categoryManager)->getCategoriesAction($paramFetcher));
    }

    public function testGetCategoryAction(): void
    {
        $category = $this->createMock(CategoryInterface::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('find')->willReturn($category);

        $this->assertSame($category, $this->createCategoryController($categoryManager)->getCategoryAction(1));
    }

    /**
     * @dataProvider getIdsForNotFound
     */
    public function testGetCategoryNotFoundExceptionAction($identifier, string $message): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage($message);

        $this->createCategoryController()->getCategoryAction($identifier);
    }

    /**
     * @phpstan-return list<array{mixed, string}>
     */
    public function getIdsForNotFound(): array
    {
        return [
            [42, 'Category not found for identifier 42.'],
            ['42', "Category not found for identifier '42'."],
            [null, 'Category not found for identifier NULL.'],
            ['', "Category not found for identifier ''."],
        ];
    }

    public function testPostCategoryAction(): void
    {
        $category = $this->createMock(CategoryInterface::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('save')->willReturn($category);

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $form->expects($this->once())->method('getData')->willReturn($category);
        $form->expects($this->once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createCategoryController($categoryManager, $formFactory)->postCategoryAction(new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostCategoryInvalidAction(): void
    {
        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->never())->method('save')->willReturn($categoryManager);

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(false);
        $form->expects($this->once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createCategoryController($categoryManager, $formFactory)->postCategoryAction(new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutCategoryAction(): void
    {
        $category = $this->createMock(CategoryInterface::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('find')->willReturn($category);
        $categoryManager->expects($this->once())->method('save')->willReturn($category);

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $form->expects($this->once())->method('getData')->willReturn($category);
        $form->expects($this->once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createCategoryController($categoryManager, $formFactory)->putCategoryAction(1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutPostInvalidAction(): void
    {
        $category = $this->createMock(CategoryInterface::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('find')->willReturn($category);
        $categoryManager->expects($this->never())->method('save')->willReturn($category);

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(false);
        $form->expects($this->once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createCategoryController($categoryManager, $formFactory)->putCategoryAction(1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteCategoryAction(): void
    {
        $category = $this->createMock(CategoryInterface::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('find')->willReturn($category);
        $categoryManager->expects($this->once())->method('delete');

        $view = $this->createCategoryController($categoryManager)->deleteCategoryAction(1);

        $this->assertSame(['deleted' => true], $view);
    }

    public function testDeleteCategoryInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $categoryManager->expects($this->once())->method('find')->willReturn(null);
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
