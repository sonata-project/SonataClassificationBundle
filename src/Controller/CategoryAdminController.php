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

namespace Sonata\ClassificationBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Category Admin Controller.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class CategoryAdminController extends Controller
{
    public static function getSubscribedServices(): array
    {
        return [
            'sonata.classification.manager.category' => CategoryManagerInterface::class,
            'sonata.classification.manager.context' => ContextManagerInterface::class,
        ] + parent::getSubscribedServices();
    }

    public function listAction(Request $request): Response
    {
        if (!$request->get('filter') && !$request->get('filters')) {
            return new RedirectResponse($this->admin->generateUrl('tree', $request->query->all()));
        }

        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        $datagrid = $this->admin->getDatagrid();
        $datagridValues = $datagrid->getValues();

        $datagridContextIsSet = isset($datagridValues['context']['value']) && !empty($datagridValues['context']['value']);

        //ignore `context` persistent parameter if datagrid `context` value is set
        if ($this->admin->getPersistentParameter('context') && !$datagridContextIsSet) {
            $datagrid->setValue('context', null, $this->admin->getPersistentParameter('context'));
        }

        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->setFormTheme($formView, $this->admin->getFilterTheme());

        return $this->renderWithExtraParams($this->admin->getTemplateRegistry()->getTemplate('list'), [
            'action' => 'list',
            'form' => $formView,
            'datagrid' => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ]);
    }

    public function treeAction(Request $request): Response
    {
        $categoryManager = $this->get('sonata.classification.manager.category');
        $currentContext = false;
        if ($context = $request->get('context')) {
            $currentContext = $this->get('sonata.classification.manager.context')->find($context);
        }

        // all root categories.
        $rootCategoriesSplitByContexts = $categoryManager->getRootCategoriesSplitByContexts(false);

        // root categories inside the current context
        $currentCategories = [];

        if (!$currentContext && !empty($rootCategoriesSplitByContexts)) {
            $currentCategories = current($rootCategoriesSplitByContexts);
            $currentContext = current($currentCategories)->getContext();
        } else {
            foreach ($rootCategoriesSplitByContexts as $contextId => $contextCategories) {
                if ($currentContext->getId() !== $contextId) {
                    continue;
                }

                foreach ($contextCategories as $category) {
                    if ($currentContext->getId() !== $category->getContext()->getId()) {
                        continue;
                    }

                    $currentCategories[] = $category;
                }
            }
        }

        $datagrid = $this->admin->getDatagrid();

        if ($this->admin->getPersistentParameter('context')) {
            $datagrid->setValue('context', null, $this->admin->getPersistentParameter('context'));
        }

        $formView = $datagrid->getForm()->createView();

        $this->setFormTheme($formView, $this->admin->getFilterTheme());

        return $this->renderWithExtraParams($this->admin->getTemplateRegistry()->getTemplate('tree'), [
            'action' => 'tree',
            'current_categories' => $currentCategories,
            'root_categories' => $rootCategoriesSplitByContexts,
            'current_context' => $currentContext,
            'form' => $formView,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ]);
    }
}
