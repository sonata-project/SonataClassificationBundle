<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\CoreBundle\Form\FormHelper;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CategoryController.
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class CategoryController
{
    /**
     * @var CategoryManagerInterface
     */
    protected $categoryManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Constructor.
     *
     * @param CategoryManagerInterface $categoryManager
     * @param FormFactoryInterface     $formFactory
     */
    public function __construct(CategoryManagerInterface $categoryManager, FormFactoryInterface $formFactory)
    {
        $this->categoryManager = $categoryManager;
        $this->formFactory     = $formFactory;
    }

    /**
     * Retrieves the list of categories (paginated) based on criteria.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}}
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for category list pagination")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of categories by page")
     * @QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/Disabled categories filter")
     * @QueryParam(name="context", requirements="\S+", nullable=true, strict=true, description="Context of categories")
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return PagerInterface
     */
    public function getCategoriesAction(ParamFetcherInterface $paramFetcher)
    {
        $page  = $paramFetcher->get('page');
        $count = $paramFetcher->get('count');

        /** @var PagerInterface $categoriesPager */
        $categoriesPager = $this->categoryManager->getPager($this->filterCriteria($paramFetcher), $page, $count);

        return $categoriesPager;
    }

    /**
     * Retrieves a specific category.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="category id"}
     *  },
     *  output={"class"="Sonata\ClassificationBundle\Model\Category", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when category is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return CategoryInterface
     */
    public function getCategoryAction($id)
    {
        return $this->getCategory($id);
    }

    /**
     * Adds a category.
     *
     * @ApiDoc(
     *  input={"class"="sonata_classification_api_form_category", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\ClassificationBundle\Model\Category", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while category creation",
     *      404="Returned when unable to find category"
     *  }
     * )
     *
     * @param Request $request A Symfony request
     *
     * @return CategoryInterface
     *
     * @throws NotFoundHttpException
     */
    public function postCategoryAction(Request $request)
    {
        return $this->handleWriteCategory($request);
    }

    /**
     * Updates a category.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="category identifier"}
     *  },
     *  input={"class"="sonata_classification_api_form_category", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\ClassificationBundle\Model\Category", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while category update",
     *      404="Returned when unable to find category"
     *  }
     * )
     *
     * @param int     $id      A Category identifier
     * @param Request $request A Symfony request
     *
     * @return CategoryInterface
     *
     * @throws NotFoundHttpException
     */
    public function putCategoryAction($id, Request $request)
    {
        return $this->handleWriteCategory($request, $id);
    }

    /**
     * Deletes a category.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="category identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when category is successfully deleted",
     *      400="Returned when an error has occurred while category deletion",
     *      404="Returned when unable to find category"
     *  }
     * )
     *
     * @param int $id A Category identifier
     *
     * @return View
     *
     * @throws NotFoundHttpException
     */
    public function deleteCategoryAction($id)
    {
        $category = $this->getCategory($id);

        $this->categoryManager->delete($category);

        return array('deleted' => true);
    }

    /**
     * Filters criteria from $paramFetcher to be compatible with the Pager criteria.
     *
     * @param ParamFetcherInterface $paramFetcher
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
     * Retrieves category with id $id or throws an exception if it doesn't exist.
     *
     * @param int $id A Category identifier
     *
     * @return CategoryInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getCategory($id)
    {
        $category = $this->categoryManager->find($id);

        if (null === $category) {
            throw new NotFoundHttpException(sprintf('Category (%d) not found', $id));
        }

        return $category;
    }

    /**
     * Write a category, this method is used by both POST and PUT action methods.
     *
     * @param Request  $request Symfony request
     * @param int|null $id      A category identifier
     *
     * @return View|FormInterface
     */
    protected function handleWriteCategory($request, $id = null)
    {
        $category = $id ? $this->getCategory($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_classification_api_form_category', $category, array(
            'csrf_protection' => false,
        ));

        FormHelper::removeFields($request->request->all(), $form);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $category = $form->getData();
            $this->categoryManager->save($category);

            $view = \FOS\RestBundle\View\View::create($category);
            $serializationContext = SerializationContext::create();
            $serializationContext->setGroups(array('sonata_api_read'));
            $serializationContext->enableMaxDepthChecks();
            $view->setSerializationContext($serializationContext);

            return $view;
        }

        return $form;
    }
}
