<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Tests;

use Sonata\AdminBundle\Admin\Pool;
use Sonata\ClassificationBundle\Controller\CategoryAdminController;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class CategoryControllerTest.
 *
 * @author Alexandre Tranchant <alexandre.tranchant@gmail.com>
 */
class CategoryAdminControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var CategoryAdmin
     */
    private $admin;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var \Sonata\ClassificationBundle\Controller\CategoryAdminController
     */
    private $categoryAdminController;

    /**
     * @var CsrfProviderInterface
     */
    private $csrfProvider;

    /**
     * @var CategoryManagerInterface
     */
    private $categoryManager;

    /**
     * @var ContextManagerInterface
     */
    private $contextManager;

    /**
     * @var DatagridInterface
     */
    private $dataGrid;

    /**
     * @var FormView
     */
    private $view;

    /**
     * @var Form
     */
    private $form;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->categoryAdminController = new CategoryAdminController();

        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->categoryManager = $this->getMockBuilder('Sonata\ClassificationBundle\Entity\CategoryManager')
            ->disableOriginalConstructor()
            ->getMock();
        $categoryManager = $this->categoryManager;

        $this->contextManager = $this->getMockBuilder('Sonata\ClassificationBundle\Entity\ContextManager')
            ->disableOriginalConstructor()
            ->getMock();
        $contextManager = $this->contextManager;

        $this->view = $this->getMock('Symfony\Component\Form\FormView');

        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->form->expects($this->any())
            ->method('createView')
            ->will($this->returnValue($this->view));

        $this->dataGrid = $this->getMock('\Sonata\AdminBundle\Datagrid\DatagridInterface');
        $this->dataGrid->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue($this->form));

        $this->request = new Request();
        $this->pool    = new Pool($this->container, 'title', 'logo.png');
        $this->pool->setAdminServiceIds(array('foo.admin'));
        $this->request->attributes->set('_sonata_admin', 'foo.admin');
        $this->admin      = $this->getMockBuilder('Sonata\ClassificationBundle\Admin\CategoryAdmin')
            ->disableOriginalConstructor()
            ->getMock();

        $this->admin->expects($this->any())
            ->method('getPersistentParameter')
            ->will($this->returnValue('persistentParameter'));
        $this->admin->expects($this->any())
            ->method('getDataGrid')
            ->will($this->returnValue($this->dataGrid));

        $params = array();
        $template = '';

        $templating = $this->getMock(
            'Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine',
            array(),
            array($this->container, array())
        );

        $templating->expects($this->any())
            ->method('renderResponse')
            ->will($this->returnCallback(function (
                $view,
                array $parameters = array(),
                Response $response = null
            ) use (
                &$params,
                &$template
            ) {
                $template = $view;

                if (null === $response) {
                    $response = new Response();
                }

                $params = $parameters;

                return $response;
            }));

        $twig = $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $twigRenderer = $this->getMock('Symfony\Bridge\Twig\Form\TwigRendererInterface');

        $formExtension = new FormExtension($twigRenderer);

        $twig->expects($this->any())
            ->method('getExtension')
            ->will($this->returnCallback(function ($name) use ($formExtension) {
                switch ($name) {
                    case 'form':
                        return $formExtension;
                }

                return;
            }));

        $this->csrfProvider = $this->getMock(
            'Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface'
        );

        $this->csrfProvider->expects($this->any())
            ->method('generateCsrfToken')
            ->will($this->returnCallback(function ($intention) {
                return 'csrf-token-123_'.$intention;
            }));

        $this->csrfProvider->expects($this->any())
            ->method('isCsrfTokenValid')
            ->will($this->returnCallback(function ($intention, $token) {
                if ($token == 'csrf-token-123_'.$intention) {
                    return true;
                }

                return false;
            }));

        // php 5.3 BC
        $pool = $this->pool;
        $admin = $this->admin;
        $request = $this->request;
        $csrfProvider = $this->csrfProvider;

        $requestStack = null;
        if (Kernel::MINOR_VERSION > 3) {
            $requestStack = new \Symfony\Component\HttpFoundation\RequestStack();
            $requestStack->push($request);
        }

        $this->container->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($id) use (
                $pool,
                $admin,
                $request,
                $requestStack,
                $templating,
                $twig,
                $csrfProvider,
                $categoryManager,
                $contextManager
            ) {
                switch ($id) {
                    case 'sonata.admin.pool':
                        return $pool;
                    case 'foo.admin':
                        return $admin;
                    case 'request':
                        return $request;
                    case 'request_stack':
                        return $requestStack;
                    case 'templating':
                        return $templating;
                    case 'twig':
                        return $twig;
                    case 'form.csrf_provider':
                        return $csrfProvider;
                    case 'sonata.classification.manager.category':
                        return $categoryManager;
                    case 'sonata.classification.manager.context':
                        return $contextManager;
                }

                return;
            }));

        $this->categoryAdminController->setContainer($this->container);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->categoryAdminController = null;
        parent::tearDown();
    }

    /**
     * Tests CategoryAdminController->tree() without data.
     */
    public function testTreeWithoutCategoryAndContext()
    {
        $this->categoryManager->expects($this->any())
            ->method('getRootCategories')
            ->will($this->returnValue(array()));
        $this->contextManager->expects($this->any())
            ->method('find')
            ->will($this->returnValue(false));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $this->categoryAdminController->treeAction($this->request));
    }

    public function getCsrfProvider()
    {
        return $this->csrfProvider;
    }
}
