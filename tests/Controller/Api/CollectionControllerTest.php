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
use Sonata\ClassificationBundle\Controller\Api\CollectionController;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class CollectionControllerTest extends TestCase
{
    public function testGetCollectionsAction()
    {
        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $pager = $this->createMock(Pager::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createCollectionController($collectionManager)->getCollectionsAction($paramFetcher));
    }

    public function testGetCollectionAction()
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects($this->once())->method('find')->will($this->returnValue($collection));

        $this->assertEquals($collection, $this->createCollectionController($collectionManager)->getCollectionAction(1));
    }

    public function testGetCollectionNotFoundExceptionAction()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Collection (42) not found');

        $this->createCollectionController()->getCollectionAction(42);
    }

    public function testPostCollectionAction()
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects($this->once())->method('save')->will($this->returnValue($collection));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($collection));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCollectionController($collectionManager, $formFactory)->postCollectionAction(new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostCollectionInvalidAction()
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects($this->never())->method('save')->will($this->returnValue($collectionManager));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCollectionController($collectionManager, $formFactory)->postCollectionAction(new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutCollectionAction()
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects($this->once())->method('find')->will($this->returnValue($collection));
        $collectionManager->expects($this->once())->method('save')->will($this->returnValue($collection));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($collection));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCollectionController($collectionManager, $formFactory)->putCollectionAction(1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutPostInvalidAction()
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects($this->once())->method('find')->will($this->returnValue($collection));
        $collectionManager->expects($this->never())->method('save')->will($this->returnValue($collection));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue([]));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCollectionController($collectionManager, $formFactory)->putCollectionAction(1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteCollectionAction()
    {
        $collection = $this->createMock(CollectionInterface::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects($this->once())->method('find')->will($this->returnValue($collection));
        $collectionManager->expects($this->once())->method('delete');

        $view = $this->createCollectionController($collectionManager)->deleteCollectionAction(1);

        $this->assertEquals(['deleted' => true], $view);
    }

    public function testDeleteCollectionInvalidAction()
    {
        $this->expectException(NotFoundHttpException::class);

        $collectionManager = $this->createMock(CollectionManagerInterface::class);
        $collectionManager->expects($this->once())->method('find')->will($this->returnValue(null));
        $collectionManager->expects($this->never())->method('delete');

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
