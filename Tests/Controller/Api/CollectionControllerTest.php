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

use Sonata\ClassificationBundle\Controller\Api\CollectionController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CollectionControllerTest.
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class CollectionControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCollectionsAction()
    {
        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $pager = $this->getMockBuilder('Sonata\AdminBundle\Datagrid\Pager')->disableOriginalConstructor()->getMock();

        $collectionManager = $this->getMock('Sonata\ClassificationBundle\Model\CollectionManagerInterface');
        $collectionManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createCollectionController($collectionManager)->getCollectionsAction($paramFetcher));
    }

    public function testGetCollectionAction()
    {
        $collection = $this->getMock('Sonata\ClassificationBundle\Model\CollectionInterface');

        $collectionManager = $this->getMock('Sonata\ClassificationBundle\Model\CollectionManagerInterface');
        $collectionManager->expects($this->once())->method('find')->will($this->returnValue($collection));

        $this->assertEquals($collection, $this->createCollectionController($collectionManager)->getCollectionAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Collection (42) not found
     */
    public function testGetCollectionNotFoundExceptionAction()
    {
        $this->createCollectionController()->getCollectionAction(42);
    }

    public function testPostCollectionAction()
    {
        $collection = $this->getMock('Sonata\ClassificationBundle\Model\CollectionInterface');

        $collectionManager = $this->getMock('Sonata\ClassificationBundle\Model\CollectionManagerInterface');
        $collectionManager->expects($this->once())->method('save')->will($this->returnValue($collection));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($collection));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCollectionController($collectionManager, $formFactory)->postCollectionAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostCollectionInvalidAction()
    {
        $collection = $this->getMock('Sonata\ClassificationBundle\Model\CollectionInterface');

        $collectionManager = $this->getMock('Sonata\ClassificationBundle\Model\CollectionManagerInterface');
        $collectionManager->expects($this->never())->method('save')->will($this->returnValue($collectionManager));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCollectionController($collectionManager, $formFactory)->postCollectionAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutCollectionAction()
    {
        $collection = $this->getMock('Sonata\ClassificationBundle\Model\CollectionInterface');

        $collectionManager = $this->getMock('Sonata\ClassificationBundle\Model\CollectionManagerInterface');
        $collectionManager->expects($this->once())->method('find')->will($this->returnValue($collection));
        $collectionManager->expects($this->once())->method('save')->will($this->returnValue($collection));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($collection));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCollectionController($collectionManager, $formFactory)->putCollectionAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutPostInvalidAction()
    {
        $collection = $this->getMock('Sonata\ClassificationBundle\Model\CollectionInterface');

        $collectionManager = $this->getMock('Sonata\ClassificationBundle\Model\CollectionManagerInterface');
        $collectionManager->expects($this->once())->method('find')->will($this->returnValue($collection));
        $collectionManager->expects($this->never())->method('save')->will($this->returnValue($collection));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->once())->method('all')->will($this->returnValue(array()));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCollectionController($collectionManager, $formFactory)->putCollectionAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteCollectionAction()
    {
        $collection = $this->getMock('Sonata\ClassificationBundle\Model\CollectionInterface');

        $collectionManager = $this->getMock('Sonata\ClassificationBundle\Model\CollectionManagerInterface');
        $collectionManager->expects($this->once())->method('find')->will($this->returnValue($collection));
        $collectionManager->expects($this->once())->method('delete');

        $view = $this->createCollectionController($collectionManager)->deleteCollectionAction(1);

        $this->assertEquals(array('deleted' => true), $view);
    }

    public function testDeleteCollectionInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $collectionManager = $this->getMock('Sonata\ClassificationBundle\Model\CollectionManagerInterface');
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
            $collectionManager = $this->getMock('Sonata\ClassificationBundle\Model\CollectionManagerInterface');
        }
        if (null === $formFactory) {
            $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new CollectionController($collectionManager, $formFactory);
    }
}
