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

use BadMethodCallException;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

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
     * @var AdminInterface|null
     */
    private $categoryAdmin;

    /**
     * @param string|Environment                               $twigOrDeprecatedName
     * @param EngineInterface|ContextManagerInterface          $contextManagerOrDeprecatedTemplating
     * @param ContextManagerInterface|CategoryManagerInterface $categoryManagerOrDeprecatedContextManager
     * @param CategoryManagerInterface|AdminInterface|null     $categoryAdminOrDeprecatedCategoryManager
     * @param AdminInterface|null                              $deprecatedCategoryAdmin
     */
    public function __construct(
        $twigOrDeprecatedName,
        $contextManagerOrDeprecatedTemplating,
        $categoryManagerOrDeprecatedContextManager,
        $categoryAdminOrDeprecatedCategoryManager,
        $deprecatedCategoryAdmin = null
    ) {
        // NEXT_MAJOR: remove the if block
        if (\is_string($twigOrDeprecatedName)) {
            parent::__construct(
                $twigOrDeprecatedName,
                $contextManagerOrDeprecatedTemplating,
                $categoryManagerOrDeprecatedContextManager
            );

            $this->categoryManager = $categoryAdminOrDeprecatedCategoryManager;
            $this->categoryAdmin = $deprecatedCategoryAdmin;
        } else {
            parent::__construct(
                $twigOrDeprecatedName,
                $contextManagerOrDeprecatedTemplating
            );

            $this->categoryManager = $categoryManagerOrDeprecatedContextManager;
            $this->categoryAdmin = $categoryAdminOrDeprecatedCategoryManager;
        }
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null)
    {
        $category = $this->getCategory($blockContext->getSetting('categoryId'), $blockContext->getSetting('category'));
        $root = $this->categoryManager->getRootCategory($blockContext->getSetting('context'));

        return $this->renderResponse($blockContext->getTemplate(), [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'category' => $category,
            'root' => $root,
        ], $response);
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        if (null === $this->categoryAdmin) {
            throw new BadMethodCallException('You need the sonata-project/admin-bundle library to edit this block.');
        }

        $adminField = $this->getFormAdminType($formMapper, $this->categoryAdmin, 'categoryId', 'category', [
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

        $formMapper->add(
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

    public function configureSettings(OptionsResolver $resolver)
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

    public function load(BlockInterface $block)
    {
        if (is_numeric($block->getSetting('categoryId'))) {
            $block->setSetting('categoryId', $this->getCategory($block->getSetting('categoryId')));
        }
    }

    public function prePersist(BlockInterface $block)
    {
        $this->resolveIds($block);
    }

    public function preUpdate(BlockInterface $block)
    {
        $this->resolveIds($block);
    }

    public function getBlockMetadata($code = null)
    {
        $description = (null !== $code ? $code : $this->getName());

        return new Metadata($this->getName(), $description, false, 'SonataClassificationBundle', [
            'class' => 'fa fa-folder-open-o',
        ]);
    }

    /**
     * @param CategoryInterface|int $id
     * @param mixed                 $default
     *
     * @return CategoryInterface
     */
    final protected function getCategory($id, $default = null)
    {
        if ($id instanceof CategoryInterface) {
            return $id;
        }

        if (is_numeric($id)) {
            return $this->categoryManager->find($id);
        }

        if ($default instanceof CategoryInterface) {
            return $default;
        }

        return null;
    }

    private function resolveIds(BlockInterface $block)
    {
        $block->setSetting(
            'categoryId',
            \is_object($block->getSetting('categoryId')) ? $block->getSetting('categoryId')->getId() : null
        );
    }
}
