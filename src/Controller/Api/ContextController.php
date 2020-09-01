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

namespace Sonata\ClassificationBundle\Controller\Api;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\ClassificationBundle\Form\FormHelper;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Thomas Rabaix <thomas.rabaix@gmail.com>
 */
class ContextController
{
    /**
     * @var ContextManagerInterface
     */
    protected $contextManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct(ContextManagerInterface $contextManager, FormFactoryInterface $formFactory)
    {
        $this->contextManager = $contextManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Retrieves the list of contexts (paginated) based on criteria.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}}
     * )
     *
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Page for context list pagination")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Number of contexts by page")
     * @Rest\QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/Disabled contexts filter")
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getContextsAction(ParamFetcherInterface $paramFetcher)
    {
        $page = $paramFetcher->get('page');
        $count = $paramFetcher->get('count');

        /** @var PagerInterface $contextsPager */
        $contextsPager = $this->contextManager->getPager($this->filterCriteria($paramFetcher), $page, $count);

        return $contextsPager;
    }

    /**
     * Retrieves a specific context.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Context identifier"}
     *  },
     *  output={"class"="Sonata\ClassificationBundle\Model\Context", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when context is not found"
     *  }
     * )
     *
     * @Rest\View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id Context identifier
     *
     * @return ContextInterface
     */
    public function getContextAction($id)
    {
        return $this->getContext($id);
    }

    /**
     * Adds a context.
     *
     * @ApiDoc(
     *  input={"class"="sonata_classification_api_form_context", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\ClassificationBundle\Model\Context", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while context creation",
     *      404="Returned when unable to find context"
     *  }
     * )
     *
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return ContextInterface
     */
    public function postContextAction(Request $request)
    {
        return $this->handleWriteContext($request);
    }

    /**
     * Updates a context.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Context identifier"}
     *  },
     *  input={"class"="sonata_classification_api_form_context", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\ClassificationBundle\Model\Context", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while context update",
     *      404="Returned when unable to find context"
     *  }
     * )
     *
     * @param string  $id      Context identifier
     * @param Request $request Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return ContextInterface
     */
    public function putContextAction($id, Request $request)
    {
        return $this->handleWriteContext($request, $id);
    }

    /**
     * Deletes a context.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Context identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when context is successfully deleted",
     *      400="Returned when an error has occurred while context deletion",
     *      404="Returned when unable to find context"
     *  }
     * )
     *
     * @param string $id Context identifier
     *
     * @throws NotFoundHttpException
     *
     * @return Rest\View
     */
    public function deleteContextAction($id)
    {
        $context = $this->getContext($id);

        $this->contextManager->delete($context);

        return ['deleted' => true];
    }

    /**
     * Filters criteria from $paramFetcher to be compatible with the Pager criteria.
     *
     * @return array The filtered criteria
     */
    protected function filterCriteria(ParamFetcherInterface $paramFetcher)
    {
        $criteria = $paramFetcher->all();

        unset($criteria['page'], $criteria['count']);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        return $criteria;
    }

    /**
     * Retrieves context with id $id or throws an exception if it doesn't exist.
     *
     * @param string $id Context identifier
     *
     * @throws NotFoundHttpException
     *
     * @return ContextInterface
     */
    protected function getContext($id)
    {
        $context = $this->contextManager->find($id);

        if (null === $context) {
            throw new NotFoundHttpException(sprintf('Context (%d) not found', $id));
        }

        return $context;
    }

    /**
     * Write a context, this method is used by both POST and PUT action methods.
     *
     * @param Request     $request Symfony request
     * @param string|null $id      context identifier
     *
     * @return FormInterface
     */
    protected function handleWriteContext($request, $id = null)
    {
        $context = $id ? $this->getContext($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_classification_api_form_context', $context, [
            'csrf_protection' => false,
        ]);

        FormHelper::removeFields($request->request->all(), $form);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $context = $form->getData();
            $this->contextManager->save($context);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);

            $view = View::create($context);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }
}
