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

namespace Sonata\ClassificationBundle\Tests;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\ClassificationBundle\Admin\CategoryAdmin;
use Sonata\ClassificationBundle\Controller\CategoryAdminController;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\ClassificationBundle\Entity\ContextManager;
use Sonata\ClassificationBundle\Model\Category;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;

/**
 * @author Dariusz Markowicz <dmarkowicz77@gmail.com>
 */
class CategoryAdminControllerTest extends TestCase
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var AdminInterface
     */
    private $admin;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $template;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfProvider;

    /**
     * @var CategoryAdminController
     */
    private $controller;

    /**
     * @var CategoryManagerInterface
     */
    private $categoryManager;

    /**
     * @var ContextManagerInterface
     */
    private $contextManager;

    /**
     * Based on Sonata\AdminBundle\Tests\Controller\CRUDControllerTest.
     */
    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->csrfProvider = $this->createMock(CsrfTokenManagerInterface::class);
        $this->admin = $this->createMock(CategoryAdmin::class);
        $this->categoryManager = $this->createMock(CategoryManager::class);
        $this->contextManager = $this->createMock(ContextManager::class);

        $this->request = new Request();
        $this->requestStack = new RequestStack();
        $this->requestStack->push($this->request);
        $this->pool = new Pool($this->container, 'title', 'logo.png');
        $this->pool->setAdminServiceIds(['foo.admin']);
        $this->request->attributes->set('_sonata_admin', 'foo.admin');
        $this->parameters = [];
        $this->template = '';

        $twig = $this->createMock(Environment::class);
        $formRenderer = $this->createMock(FormRenderer::class);

        $twig->expects($this->any())
            ->method('getRuntime')
            ->willReturn($formRenderer);

        $this->csrfProvider->expects($this->any())
            ->method('getToken')
            ->willReturnCallback(static function ($intention) {
                return new CsrfToken($intention, 'csrf-token-123_'.$intention);
            });

        $this->csrfProvider->expects($this->any())
            ->method('isTokenValid')
            ->willReturnCallback(static function (CsrfToken $token) {
                if ($token->getValue() === 'csrf-token-123_'.$token->getId()) {
                    return true;
                }

                return false;
            });

        $this->admin->expects($this->any())
            ->method('getCode')
            ->willReturn('admin_code');

        $this->container->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($id) use ($twig) {
                switch ($id) {
                    case 'sonata.admin.pool':
                        return $this->pool;
                    case 'request':
                        return $this->request;
                    case 'request_stack':
                        return $this->requestStack;
                    case 'foo.admin':
                        return $this->admin;
                    case 'twig':
                        return $twig;
                    case 'form.csrf_provider':
                    case 'security.csrf.token_manager':
                        return $this->csrfProvider;
                    case 'sonata.classification.manager.category':
                        return $this->categoryManager;
                    case 'sonata.classification.manager.context':
                        return $this->contextManager;
                    case 'admin_code.template_registry':
                        return new TemplateRegistry();
                }
            });

        // php 5.3
        $tthis = $this;

        $this->container->expects($this->any())
            ->method('has')
            ->willReturnCallback(static function ($id) use ($tthis) {
                if ('security.csrf.token_manager' === $id && null !== $tthis->getCsrfProvider()) {
                    return true;
                }

                if ('twig' === $id) {
                    return true;
                }

                return false;
            });

        $this->admin->expects($this->any())
            ->method('generateUrl')
            ->willReturnCallback(
                static function ($name, array $parameters = [], $absolute = false) {
                    $result = $name;
                    if (!empty($parameters)) {
                        $result .= '?'.http_build_query($parameters);
                    }

                    return $result;
                }
            );

        $this->admin->expects($this->any())
            ->method('getTemplate')
            ->willReturn('@SonataClassification/CategoryAdmin/list.html.twig');

        $this->controller = new CategoryAdminController();
        $this->controller->setContainer($this->container);
    }

    protected function tearDown(): void
    {
        $this->controller = null;
    }

    public function testListActionWithoutFilter(): void
    {
        $this->request->query->set('hide_context', '0');

        $result = $this->controller->listAction($this->request);
        $this->assertInstanceOf(
            RedirectResponse::class,
            $result
        );
        $this->assertSame('tree?hide_context=0', $result->getTargetUrl());
    }

    /**
     * @dataProvider listActionData
     */
    public function testListAction($context): void
    {
        $this->request->query->set('_list_mode', 'list');
        $this->request->query->set('filter', 'filter[context][value]='.($context ? $context : ''));

        $datagrid = $this->createMock(DatagridInterface::class);

        $form = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->getMock();

        $form->expects($this->once())
             ->method('createView')
             ->willReturn(new FormView());

        $this->admin->expects($this->once())
            ->method('getDatagrid')
            ->willReturn($datagrid);

        $datagrid->expects($this->once())
            ->method('getForm')
            ->willReturn($form);

        $datagrid->expects($this->once())
            ->method('getValues')
            ->willReturn([
                'context' => [
                    'value' => $context ?: '',
                ],
            ]);

        $this->admin->expects($this->any())
            ->method('getPersistentParameter')
            ->willReturn($context);

        $this->assertInstanceOf(
            Response::class,
            $this->controller->listAction($this->request)
        );
    }

    public function listActionData()
    {
        return [
            'context' => ['default'],
            'no context' => [false],
        ];
    }

    /**
     * @dataProvider treeActionData
     */
    public function testTreeAction($context, $categories): void
    {
        $datagrid = $this->createMock(DatagridInterface::class);

        $form = $this->createMock(Form::class);

        $form->expects($this->once())
            ->method('createView')
            ->willReturn(new FormView());

        $this->admin->expects($this->once())
            ->method('getDatagrid')
            ->willReturn($datagrid);

        $datagrid->expects($this->once())
            ->method('getForm')
            ->willReturn($form);

        $this->admin->expects($this->any())
            ->method('getPersistentParameter')
            ->willReturn('default');

        if ($context) {
            $contextMock = $this->getContextMock($context);
            $this->request->query->set('context', $contextMock->getId());
            $this->contextManager->expects($this->any())
                ->method('find')
                ->willReturn($contextMock);
        } else {
            $this->request->query->remove('context');
            $this->contextManager->expects($this->any())
                ->method('find')
                ->willReturn(false);
        }

        $categoriesMock = [];
        foreach ($categories as $category) {
            $categoryMock = $this->getMockForAbstractClass(Category::class);
            $categoryMock->setName($category[0]);
            if ($category[1]) {
                $categoryMock->setContext($this->getContextMock($category[1]));
            }
            $categoryMock->setEnabled(true);
            $categoriesMock[$categoryMock->getContext()->getId()][] = $categoryMock;
        }

        $this->categoryManager->expects($this->any())
            ->method('getRootCategoriesSplitByContexts')
            ->willReturn($categoriesMock);

        $this->assertInstanceOf(
            Response::class,
            $this->controller->treeAction($this->request)
        );
    }

    public function treeActionData()
    {
        return [
            'context and no categories' => ['default', []],
            'no context and no categories' => [false, []],
            'context and categories' => ['default', [
                ['First Category', 'other'],
                ['Second Category', 'default'],
            ]],
            'no context and categories' => [false, [
                ['First Category', 'other'],
                ['Second Category', 'default'],
            ]],
        ];
    }

    public function getCsrfProvider()
    {
        return $this->csrfProvider;
    }

    private function getContextMock($id)
    {
        $contextMock = $this->createMock(ContextInterface::class);
        $contextMock->expects($this->any())->method('getId')->willReturn($id);
        $contextMock->setName($id);
        $contextMock->setEnabled(true);

        return $contextMock;
    }
}
