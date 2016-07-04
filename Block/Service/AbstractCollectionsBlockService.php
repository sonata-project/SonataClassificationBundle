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
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Christian Gripp <mail@core23.de>
 */
abstract class AbstractCollectionsBlockService extends AbstractClassificationBlockService
{
    /**
     * @var CollectionManagerInterface
     */
    private $collectionManager;

    /**
     * @var AdminInterface
     */
    private $collectionAdmin;

    /**
     * @param string                     $name
     * @param EngineInterface            $templating
     * @param ContextManagerInterface    $contextManager
     * @param CollectionManagerInterface $collectionManager
     * @param AdminInterface             $collectionAdmin
     */
    public function __construct($name, EngineInterface $templating, ContextManagerInterface $contextManager, CollectionManagerInterface $collectionManager, AdminInterface $collectionAdmin)
    {
        parent::__construct($name, $templating, $contextManager);

        $this->collectionManager = $collectionManager;
        $this->collectionAdmin = $collectionAdmin;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $collection = $this->getCollection($blockContext->getSetting('collectionId'), $blockContext->getSetting('collection'));
        $collections = $this->contextManager->findBy(array(
            'enabled' => true,
            'context' => $blockContext->getSetting('context'),
        ));

        return $this->renderResponse($blockContext->getTemplate(), array(
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'collection' => $collection,
            'collections' => $collections,
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $adminField = $this->getFormAdminType($formMapper, $this->collectionAdmin, 'collectionId', 'collection', array(
            'label' => 'form.label_collection',
        ), array(
            'translation_domain' => 'SonataClassificationBundle',
            'link_parameters' => array(
                array(
                    'context' => $block->getSetting('context'),
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
            'title' => 'Collections',
            'collection' => false,
            'collectionId' => null,
            'context' => null,
            'template' => 'SonataClassificationBundle:Block:base_block_collections.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
        if (is_numeric($block->getSetting('collectionId'))) {
            $block->setSetting('collectionId', $this->getCollection($block->getSetting('collectionId')));
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
     * @param CollectionInterface|int  $id
     * @param CollectionInterface|null $default
     *
     * @return CollectionInterface
     */
    final protected function getCollection($id, CollectionInterface $default = null)
    {
        if ($id instanceof CollectionInterface) {
            return $id;
        }

        if (is_numeric($id)) {
            return $this->collectionManager->find($id);
        }

        return $default;
    }

    /**
     * @param BlockInterface $block
     */
    private function resolveIds(BlockInterface $block)
    {
        $block->setSetting(
            'collectionId',
            is_object($block->getSetting('collectionId')) ? $block->getSetting('collectionId')->getId() : null
        );
    }
}
