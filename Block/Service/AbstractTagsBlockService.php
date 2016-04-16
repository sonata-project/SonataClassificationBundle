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

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\ClassificationBundle\Admin\TagAdmin;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractTagsBlockService extends BaseClassificationBlockService
{
    /**
     * @var TagManagerInterface
     */
    private $tagManager;

    /**
     * @var TagAdmin
     */
    private $tagAdmin;

    /**
     * @param string                  $name
     * @param EngineInterface         $templating
     * @param ContextManagerInterface $contextManager
     * @param TagManagerInterface     $tagManager
     * @param TagAdmin                $tagAdmin
     */
    public function __construct(
        $name,
        EngineInterface $templating,
        ContextManagerInterface $contextManager,
        TagManagerInterface $tagManager,
        TagAdmin $tagAdmin
    ) {
        parent::__construct($name, $templating, $contextManager);

        $this->tagManager = $tagManager;
        $this->tagAdmin = $tagAdmin;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $tag = $this->getTag($blockContext->getSetting('tagId'), $blockContext->getSetting('tag'));
        $tags = $this->tagManager->findBy(array(
            'enabled' => true,
            'context' => $blockContext->getSetting('context'),
        ));

        return $this->renderResponse($blockContext->getTemplate(), array(
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'tag' => $tag,
            'tags' => $tags,
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $contextChoices = array();
        /** @var ContextInterface $context */
        foreach ($this->contextManager->findAll() as $context) {
            $contextChoices[$context->getId()] = $context->getName();
        }

        $adminField = $this->getFormAdminType($formMapper, $this->tagAdmin, 'tagId', 'tag', array(
            'label' => 'form.label_tag',
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
                    'choices' => $contextChoices,
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
            'title' => 'Tags',
            'tag' => false,
            'tagId' => null,
            'context' => null,
            'template' => 'SonataClassificationBundle:Block:base_block_tags.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
        if (is_numeric($block->getSetting('tagId', null))) {
            $block->setSetting('tagId', $this->getTag($block->getSetting('tagId', null)));
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
            'class' => 'fa fa-tags',
        ));
    }

    /**
     * @param $id
     * @param $default
     *
     * @return TagInterface
     */
    protected function getTag($id, $default = false)
    {
        if (is_object($id)) {
            return $id;
        }

        if (!is_null($id) && $id) {
            return $this->tagManager->find($id);
        }

        return $default;
    }

    /**
     * @param BlockInterface $block
     */
    private function resolveIds(BlockInterface $block)
    {
        $block->setSetting('tagId',
            is_object($block->getSetting('tagId')) ? $block->getSetting('tagId')->getId() : null
        );
    }
}
