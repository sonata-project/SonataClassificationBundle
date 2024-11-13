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
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
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
 * @phpstan-extends AbstractClassificationBlockService<CategoryInterface>
 */
abstract class AbstractCategoriesBlockService extends AbstractClassificationBlockService implements EditableBlockService
{
    /**
     * @phpstan-param AdminInterface<CategoryInterface>|null $categoryAdmin
     */
    public function __construct(
        Environment $twig,
        ContextManagerInterface $contextManager,
        private CategoryManagerInterface $categoryManager,
        private ?AdminInterface $categoryAdmin,
    ) {
        parent::__construct($twig, $contextManager);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $category = $this->getCategory($blockContext->getSetting('categoryId'), $blockContext->getSetting('category'));
        $root = current($this->categoryManager->getRootCategoriesForContext($blockContext->getSetting('context')));

        $template = $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'category' => $category,
            'root' => $root,
        ], $response);
    }

    public function configureCreateForm(FormMapper $form, BlockInterface $block): void
    {
        $this->configureEditForm($form, $block);
    }

    public function configureEditForm(FormMapper $form, BlockInterface $block): void
    {
        if (null === $this->categoryAdmin) {
            throw new \BadMethodCallException('You need the sonata-project/admin-bundle library to edit this block.');
        }

        $adminField = $this->getFormAdminType($form, $this->categoryAdmin, 'categoryId', 'category', [
            'label' => 'form.label_category',
        ], [
            'translation_domain' => 'SonataClassificationBundle',
            'link_parameters' => [
                [
                    [
                        'context' => $block->getSetting('context'),
                    ],
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
            'icon' => 'fa fa-folder-open-o',
            'class' => null,
            'category' => false,
            'categoryId' => null,
            'context' => 'default',
            'template' => '@SonataClassification/Block/base_block_categories.html.twig',
        ]);
    }

    public function load(BlockInterface $block): void
    {
        $categoryId = $block->getSetting('categoryId');
        if (\is_int($categoryId) || \is_string($categoryId)) {
            $block->setSetting('categoryId', $this->getCategory($categoryId));
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
        return new Metadata('sonata.classification.block.categories', null, null, 'SonataClassificationBundle', [
            'class' => 'fa fa-folder-open-o',
        ]);
    }

    /**
     * @param CategoryInterface|int|string|null $id
     */
    final protected function getCategory($id, mixed $default = null): ?CategoryInterface
    {
        if ($id instanceof CategoryInterface) {
            return $id;
        }

        if (null !== $id) {
            return $this->categoryManager->find($id);
        }

        if ($default instanceof CategoryInterface) {
            return $default;
        }

        return null;
    }

    private function resolveIds(BlockInterface $block): void
    {
        $block->setSetting(
            'categoryId',
            $block->getSetting('categoryId') instanceof CategoryInterface
                ? $block->getSetting('categoryId')->getId()
                : null
        );
    }
}
