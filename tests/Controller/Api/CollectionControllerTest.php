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
use Sonata\ClassificationBundle\Controller\Api\CollectionController;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
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
class CollectionControllerTest extends TestCase
{
    public function testGetCollectionsAction(): void
    {
        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects(static::once())->method('all')->willReturn([]);

        $pager = $this->createMock(Pager::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects(static::once())->method('getPager')->willReturn($pager);

        static::assertSame($pager, $this->createCollectionController($collectionManager)->getCollectionsAction($paramFetcher));
    }

    public function testGetCollectionAction(): void
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects(static::once())->method('find')->willReturn($collection);

        static::assertSame($collection, $this->createCollectionController($collectionManager)->getCollectionAction(1));
    }

    public function testGetCollectionNotFoundExceptionAction(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Collection (42) not found');

        $this->createCollectionController()->getCollectionAction(42);
    }

    public function testPostCollectionAction(): void
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects(static::once())->method('save')->willReturn($collection);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($collection);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createCollectionController($collectionManager, $formFactory)->postCollectionAction(new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPostCollectionInvalidAction(): void
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects(static::never())->method('save')->willReturn($collectionManager);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createCollectionController($collectionManager, $formFactory)->postCollectionAction(new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutCollectionAction(): void
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects(static::once())->method('find')->willReturn($collection);
        $collectionManager->expects(static::once())->method('save')->willReturn($collection);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($collection);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createCollectionController($collectionManager, $formFactory)->putCollectionAction(1, new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPutPostInvalidAction(): void
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects(static::once())->method('find')->willReturn($collection);
        $collectionManager->expects(static::never())->method('save')->willReturn($collection);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);
        $form->expects(static::once())->method('all')->willReturn([]);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createCollectionController($collectionManager, $formFactory)->putCollectionAction(1, new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteCollectionAction(): void
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects(static::once())->method('find')->willReturn($collection);
        $collectionManager->expects(static::once())->method('delete');

        $view = $this->createCollectionController($collectionManager)->deleteCollectionAction(1);

        static::assertSame(['deleted' => true], $view);
    }

    public function testDeleteCollectionInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects(static::once())->method('find')->willReturn(null);
        $collectionManager->expects(static::never())->method('delete');

        $this->createCollectionController($collectionManager)->deleteCollectionAction(1);
    }

    /**
     * Creates a new CollectionController.
     *
     * @param null $collectionManager
     * @param null $formFactory
     *
     * @return CollectionController
     */
    protected function createCollectionController($collectionManager = null, $formFactory = null)
    {
        if (null === $collectionManager) {
            $collectionManager = $this->createMock(CollectionManagerInterface::class);
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock(FormFactoryInterface::class);
        }

        return new CollectionController($collectionManager, $formFactory);
    }
}
