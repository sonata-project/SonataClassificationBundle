<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Controller;

use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Page Admin Controller
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class CategoryAdminController extends ContextAwareAdminController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction()
    {
        if (!$this->getRequest()->get('filter') && !$this->getRequest()->get('filters')) {
            return new RedirectResponse($this->admin->generateUrl('tree'));
        }

        return parent::listAction();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function treeAction(Request $request)
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $categoryManager = $this->get('sonata.classification.manager.category');

        $currentContext = false;
        if ($context = $request->get('context')) {
            $currentContext = $this->get('sonata.classification.manager.context')->find($context);
        }

        $rootCategories = $categoryManager->getRootCategories(false);

        if (!$currentContext) {
            $mainCategory = current($rootCategories);
            $currentContext = $mainCategory->getContext();
        } else {
            foreach ($rootCategories as $category) {
                if ($currentContext->getId() != $category->getContext()->getId()) {
                    continue;
                }

                $mainCategory = $category;

                break;
            }
        }

        $datagrid = $this->admin->getDatagrid();

        if ($context = $this->admin->getPersistentParameter('context')) {
            $datagrid->setValue('context', ChoiceType::TYPE_EQUAL, $context);
        }

        $formView = $datagrid->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render('SonataClassificationBundle:CategoryAdmin:tree.html.twig', array(
            'action'          => 'tree',
            'main_category'   => $mainCategory,
            'root_categories' => $rootCategories,
            'current_context' => $currentContext,
            'form'            => $formView,
            'csrf_token'      => $this->getCsrfToken('sonata.batch'),
        ));
    }
}