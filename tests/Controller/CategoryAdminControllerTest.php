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

namespace Sonata\ClassificationBundle\Tests\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Request\AdminFetcherInterface;
use Sonata\AdminBundle\Templating\MutableTemplateRegistryInterface;
use Sonata\ClassificationBundle\Controller\CategoryAdminController;
use Sonata\ClassificationBundle\Model\Category;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Symfony\Component\DependencyInjection\Container;
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
final class CategoryAdminControllerTest extends TestCase
{
    private Request $request;

    private RequestStack $requestStack;

    /**
     * @var AdminInterface<CategoryInterface>&MockObject
     */
    private $admin;

    private Pool $pool;

    private ContainerInterface $container;

    /**
     * @var CsrfTokenManagerInterface&MockObject
     */
    private $csrfProvider;

    private CategoryAdminController $controller;

    /**
     * @var CategoryManagerInterface&MockObject
     */
    private $categoryManager;

    /**
     * @var ContextManagerInterface&MockObject
     */
    private $contextManager;

    /**
     * Based on Sonata\AdminBundle\Tests\Controller\CRUDControllerTest.
     */
    protected function setUp(): void
    {
        $this->container = new Container();
        $this->csrfProvider = $this->createMock(CsrfTokenManagerInterface::class);
        $this->admin = $this->createMock(AdminInterface::class);
        $this->categoryManager = $this->createMock(CategoryManagerInterface::class);
        $this->contextManager = $this->createMock(ContextManagerInterface::class);

        $this->request = new Request();
        $this->requestStack = new RequestStack();
        $this->requestStack->push($this->request);
        $this->pool = new Pool($this->container, ['admin_code' => 'admin_code']);
        $this->request->attributes->set('_sonata_admin', 'admin_code');

        $twig = $this->createMock(Environment::class);
        $formRenderer = $this->createMock(FormRenderer::class);

        $twig->expects(static::any())
            ->method('getRuntime')
            ->willReturn($formRenderer);

        $this->csrfProvider->expects(static::any())
            ->method('getToken')
            ->willReturnCallback(static function ($intention) {
                return new CsrfToken($intention, 'csrf-token-123_'.$intention);
            });

        $this->csrfProvider->expects(static::any())
            ->method('isTokenValid')
            ->willReturnCallback(static function (CsrfToken $token) {
                if ($token->getValue() === 'csrf-token-123_'.$token->getId()) {
                    return true;
                }

                return false;
            });

        $this->admin->expects(static::any())
            ->method('getCode')
            ->willReturn('admin_code');

        $adminFetcher = $this->createMock(AdminFetcherInterface::class);
        $adminFetcher->method('get')->willReturn($this->admin);

        $this->container->set('admin_code', $this->admin);
        $this->container->set('sonata.admin.request.fetcher', $adminFetcher);
        $this->container->set('sonata.admin.pool', $this->pool);
        $this->container->set('sonata.classification.manager.category', $this->categoryManager);
        $this->container->set('sonata.classification.manager.context', $this->contextManager);
        $this->container->set('twig', $twig);
        $this->container->set('request_stack', $this->requestStack);

        $this->admin->expects(static::any())
            ->method('generateUrl')
            ->willReturnCallback(
                static function ($name, array $parameters = []) {
                    $result = $name;
                    if (!empty($parameters)) {
                        $result .= '?'.http_build_query($parameters);
                    }

                    return $result;
                }
            );

        $templateRegistry = $this->createMock(MutableTemplateRegistryInterface::class);
        $this->admin->method('hasTemplateRegistry')->willReturn(true);
        $this->admin->method('getTemplateRegistry')->willReturn($templateRegistry);

        $templateRegistry->expects(static::any())
            ->method('getTemplate')
            ->willReturn('@SonataClassification/CategoryAdmin/list.html.twig');

        $this->controller = new CategoryAdminController();
        $this->controller->setContainer($this->container);
        $this->controller->configureAdmin($this->request);
    }

    protected function tearDown(): void
    {
        unset($this->controller);
    }

    public function testListActionWithoutFilter(): void
    {
        $this->request->query->set('hide_context', '0');

        $result = $this->controller->listAction($this->request);
        static::assertInstanceOf(
            RedirectResponse::class,
            $result
        );
        static::assertSame('tree?hide_context=0', $result->getTargetUrl());
    }

    /**
     * @dataProvider listActionData
     *
     * @param string|false $context
     */
    public function testListAction($context): void
    {
        $this->request->query->set('_list_mode', 'list');
        $this->request->query->set('filter', 'filter[context][value]='.($context ?: ''));

        $datagrid = $this->createMock(DatagridInterface::class);

        $form = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->getMock();

        $form->expects(static::once())
             ->method('createView')
             ->willReturn(new FormView());

        $this->admin->expects(static::once())
            ->method('getDatagrid')
            ->willReturn($datagrid);

        $datagrid->expects(static::once())
            ->method('getForm')
            ->willReturn($form);

        $datagrid->expects(static::once())
            ->method('getValues')
            ->willReturn([
                'context' => [
                    'value' => $context ?: '',
                ],
            ]);

        $this->admin->expects(static::any())
            ->method('getPersistentParameter')
            ->willReturn($context);

        static::assertInstanceOf(
            Response::class,
            $this->controller->listAction($this->request)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function listActionData(): array
    {
        return [
            'context' => ['default'],
            'no context' => [false],
        ];
    }

    /**
     * @dataProvider treeActionData
     *
     * @param string|false          $context
     * @param array<string, string> $categories
     */
    public function testTreeAction($context, array $categories): void
    {
        $datagrid = $this->createMock(DatagridInterface::class);

        $form = $this->createMock(Form::class);

        $form->expects(static::once())
            ->method('createView')
            ->willReturn(new FormView());

        $this->admin->expects(static::once())
            ->method('getDatagrid')
            ->willReturn($datagrid);

        $datagrid->expects(static::once())
            ->method('getForm')
            ->willReturn($form);

        $this->admin->expects(static::any())
            ->method('getPersistentParameter')
            ->willReturn('default');

        if ($context) {
            $contextMock = $this->getContextMock($context);
            $this->request->query->set('context', $contextMock->getId());
            $this->contextManager->expects(static::any())
                ->method('find')
                ->willReturn($contextMock);
        } else {
            $this->request->query->remove('context');
            $this->contextManager->expects(static::any())
                ->method('find')
                ->willReturn(false);
        }

        $categoriesMock = [];
        foreach ($categories as $category) {
            $categoryMock = $this->getMockForAbstractClass(Category::class);
            $categoryMock->setName($category[0]);

            $contextId = $category[1];
            $contextMock = $this->getContextMock($contextId);
            $categoryMock->setContext($contextMock);
            $categoryMock->setEnabled(true);
            $categoriesMock[$contextId][] = $categoryMock;
        }

        $this->categoryManager->expects(static::any())
            ->method('getRootCategoriesSplitByContexts')
            ->willReturn($categoriesMock);

        static::assertInstanceOf(
            Response::class,
            $this->controller->treeAction($this->request)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function treeActionData(): array
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

    /**
     * @return ContextInterface&MockObject
     */
    private function getContextMock(string $id): ContextInterface
    {
        $contextMock = $this->createMock(ContextInterface::class);
        $contextMock->expects(static::any())->method('getId')->willReturn($id);
        $contextMock->setName($id);
        $contextMock->setEnabled(true);

        return $contextMock;
    }
}
