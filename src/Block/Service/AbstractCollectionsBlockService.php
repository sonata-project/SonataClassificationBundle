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

namespace Sonata\ClassificationBundle\Block\Service;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\EditableBlockService;
use Sonata\BlockBundle\Form\Mapper\FormMapper;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Meta\MetadataInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * @author Christian Gripp <mail@core23.de>
 *
 * @phpstan-extends AbstractClassificationBlockService<CollectionInterface>
 */
abstract class AbstractCollectionsBlockService extends AbstractClassificationBlockService implements EditableBlockService
{
    /**
     * @phpstan-param AdminInterface<CollectionInterface>|null $collectionAdmin
     */
    public function __construct(
        Environment $twig,
        ContextManagerInterface $contextManager,
        private CollectionManagerInterface $collectionManager,
        private ?AdminInterface $collectionAdmin = null,
    ) {
        parent::__construct($twig, $contextManager);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $collection = $this->getCollection($blockContext->getSetting('collectionId'), $blockContext->getSetting('collection'));
        $collections = $this->collectionManager->findBy([
            'enabled' => true,
            'context' => $blockContext->getSetting('context'),
        ]);

        $template = $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'collection' => $collection,
            'collections' => $collections,
        ], $response);
    }

    public function configureCreateForm(FormMapper $form, BlockInterface $block): void
    {
        $this->configureEditForm($form, $block);
    }

    public function configureEditForm(FormMapper $form, BlockInterface $block): void
    {
        if (null === $this->collectionAdmin) {
            throw new \BadMethodCallException('You need the sonata-project/admin-bundle library to edit this block.');
        }

        $adminField = $this->getFormAdminType($form, $this->collectionAdmin, 'collectionId', 'collection', [
            'label' => 'form.label_collection',
        ], [
            'translation_domain' => 'SonataClassificationBundle',
            'link_parameters' => [
                [
                    'context' => $block->getSetting('context'),
                ],
            ],
        ]);

        $form->add(
            'settings',
            ImmutableArrayType::class,
            [
                'keys' => [
                    ['title', TextType::class, [
                        'required' => false,
                        'label' => 'form.label_title',
                    ]],
                    ['translation_domain', TextType::class, [
                        'label' => 'form.label_translation_domain',
                        'required' => false,
                    ]],
                    ['icon', TextType::class, [
                        'label' => 'form.label_icon',
                        'required' => false,
                    ]],
                    ['class', TextType::class, [
                        'label' => 'form.label_class',
                        'required' => false,
                    ]],
                    ['context', ChoiceType::class, [
                        'label' => 'form.label_context',
                        'required' => false,
                        'choices' => $this->getContextChoices(),
                    ]],
                    [$adminField, null, []],
                ],
                'translation_domain' => 'SonataClassificationBundle',
            ]
        );
    }

    public function validate(ErrorElement $errorElement, BlockInterface $block): void
    {
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'title' => null,
            'translation_domain' => null,
            'icon' => 'fa fa-inpanel',
            'class' => null,
            'collection' => false,
            'collectionId' => null,
            'context' => null,
            'template' => '@SonataClassification/Block/base_block_collections.html.twig',
        ]);
    }

    public function load(BlockInterface $block): void
    {
        $collectionId = $block->getSetting('collectionId');
        if (\is_int($collectionId) || \is_string($collectionId)) {
            $block->setSetting('collectionId', $this->getCollection($collectionId));
        }
    }

    public function prePersist(BlockInterface $block): void
    {
        $this->resolveIds($block);
    }

    public function preUpdate(BlockInterface $block): void
    {
        $this->resolveIds($block);
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata('sonata.classification.block.collections', null, null, 'SonataClassificationBundle', [
            'class' => 'fa fa-folder-open-o',
        ]);
    }

    /**
     * @param CollectionInterface|int|string|null $id
     */
    final protected function getCollection($id, mixed $default = null): ?CollectionInterface
    {
        if ($id instanceof CollectionInterface) {
            return $id;
        }

        if (null !== $id) {
            return $this->collectionManager->find($id);
        }

        if ($default instanceof CollectionInterface) {
            return $default;
        }

        return null;
    }

    private function resolveIds(BlockInterface $block): void
    {
        $block->setSetting(
            'collectionId',
            $block->getSetting('collectionId') instanceof CollectionInterface
                ? $block->getSetting('collectionId')->getId()
                : null
        );
    }
}
