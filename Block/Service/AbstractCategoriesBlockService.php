<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Block\Service;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Christian Gripp <mail@core23.de>
 */
abstract class AbstractCategoriesBlockService extends AbstractClassificationBlockService
{
    /**
     * @var CategoryManagerInterface
     */
    private $categoryManager;

    /**
     * @var AdminInterface
     */
    private $categoryAdmin;

    /**
     * @param string                   $name
     * @param EngineInterface          $templating
     * @param ContextManagerInterface  $contextManager
     * @param CategoryManagerInterface $categoryManager
     * @param AdminInterface           $categoryAdmin
     */
    public function __construct($name, EngineInterface $templating, ContextManagerInterface $contextManager, CategoryManagerInterface $categoryManager, AdminInterface $categoryAdmin)
    {
        parent::__construct($name, $templating, $contextManager);

        $this->categoryManager = $categoryManager;
        $this->categoryAdmin = $categoryAdmin;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $category = $this->getCategory($blockContext->getSetting('categoryId'), $blockContext->getSetting('category'));
        $root = $this->categoryManager->getRootCategory($blockContext->getSetting('context'));

        return $this->renderResponse($blockContext->getTemplate(), array(
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'category' => $category,
            'root' => $root,
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $adminField = $this->getFormAdminType($formMapper, $this->categoryAdmin, 'categoryId', 'category', array(
            'label' => 'form.label_category',
        ), array(
            'translation_domain' => 'SonataClassificationBundle',
            'link_parameters' => array(
                array(
                    array(
                        'context' => $block->getSetting('context'),
                    ),
                ),
            ),
        ));

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('title', 'text', array(
                    'label' => 'form.label_title',
                    'required' => false,
                )),
                array('context', 'choice', array(
                    'label' => 'form.label_context',
                    'required' => false,
                    'choices' => $this->getContextChoices(),
                )),
                array($adminField, null, array()),
            ),
            'translation_domain' => 'SonataClassificationBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title' => 'Categories',
            'category' => false,
            'categoryId' => null,
            'context' => 'default',
            'template' => 'SonataClassificationBundle:Block:base_block_categories.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
        if (is_numeric($block->getSetting('categoryId'))) {
            $block->setSetting('categoryId', $this->getCategory($block->getSetting('categoryId')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(BlockInterface $block)
    {
        $this->resolveIds($block);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(BlockInterface $block)
    {
        $this->resolveIds($block);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        $description = (!is_null($code) ? $code : $this->getName());

        return new Metadata($this->getName(), $description, false, 'SonataClassificationBundle', array(
            'class' => 'fa fa-folder-open-o',
        ));
    }

    /**
     * @param CategoryInterface|int  $id
     * @param CategoryInterface|null $default
     *
     * @return CategoryInterface
     */
    final protected function getCategory($id, CategoryInterface $default = null)
    {
        if ($id instanceof CategoryInterface) {
            return $id;
        }

        if (is_numeric($id)) {
            return $this->categoryManager->find($id);
        }

        return $default;
    }

    /**
     * @param BlockInterface $block
     */
    private function resolveIds(BlockInterface $block)
    {
        $block->setSetting(
            'categoryId',
            is_object($block->getSetting('categoryId')) ? $block->getSetting('categoryId')->getId() : null
        );
    }
}
