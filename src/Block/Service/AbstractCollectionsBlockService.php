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
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
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
abstract class AbstractCollectionsBlockService extends AbstractClassificationBlockService
{
    /**
     * @var CollectionManagerInterface
     */
    private $collectionManager;

    /**
     * @var AdminInterface|null
     */
    private $collectionAdmin;

    /**
     * AbstractCollectionsBlockService constructor.
     *
     * @param string|Environment                                 $twigOrDeprecatedName
     * @param EngineInterface|ContextManagerInterface            $contextManagerOrDeprecatedTemplating
     * @param ContextManagerInterface|CollectionManagerInterface $collectionManagerOrDeprecatedContextManager
     * @param CollectionManagerInterface|AdminInterface|null     $collectionAdminOrDeprecatedCollectionManager
     * @param AdminInterface|null                                $deprecatedCollectionAdmin
     */
    public function __construct(
        $twigOrDeprecatedName,
        $contextManagerOrDeprecatedTemplating,
        $collectionManagerOrDeprecatedContextManager,
        $collectionAdminOrDeprecatedCollectionManager,
        $deprecatedCollectionAdmin = null
    ) {
        // NEXT_MAJOR: remove the if block
        if (\is_string($twigOrDeprecatedName)) {
            parent::__construct(
                $twigOrDeprecatedName,
                $contextManagerOrDeprecatedTemplating,
                $collectionManagerOrDeprecatedContextManager
            );

            $this->collectionManager = $collectionAdminOrDeprecatedCollectionManager;
            $this->collectionAdmin = $deprecatedCollectionAdmin;
        } else {
            parent::__construct(
                $twigOrDeprecatedName,
                $contextManagerOrDeprecatedTemplating
            );

            $this->collectionManager = $collectionManagerOrDeprecatedContextManager;
            $this->collectionAdmin = $collectionAdminOrDeprecatedCollectionManager;
        }
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null)
    {
        $collection = $this->getCollection($blockContext->getSetting('collectionId'), $blockContext->getSetting('collection'));
        $collections = $this->collectionManager->findBy([
            'enabled' => true,
            'context' => $blockContext->getSetting('context'),
        ]);

        return $this->renderResponse($blockContext->getTemplate(), [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'collection' => $collection,
            'collections' => $collections,
        ], $response);
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        if (null === $this->collectionAdmin) {
            throw new BadMethodCallException('You need the sonata-project/admin-bundle library to edit this block.');
        }

        $adminField = $this->getFormAdminType($formMapper, $this->collectionAdmin, 'collectionId', 'collection', [
            'label' => 'form.label_collection',
        ], [
            'translation_domain' => 'SonataClassificationBundle',
            'link_parameters' => [
                [
                    'context' => $block->getSetting('context'),
                ],
            ],
        ]);

        $formMapper->add(
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

    public function configureSettings(OptionsResolver $resolver)
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

    public function load(BlockInterface $block)
    {
        if (is_numeric($block->getSetting('collectionId'))) {
            $block->setSetting('collectionId', $this->getCollection($block->getSetting('collectionId')));
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
     * @param CollectionInterface|int $id
     * @param mixed                   $default
     *
     * @return CollectionInterface|null
     */
    final protected function getCollection($id, $default = null)
    {
        if ($id instanceof CollectionInterface) {
            return $id;
        }

        if (is_numeric($id)) {
            return $this->collectionManager->find($id);
        }

        if ($default instanceof CollectionInterface) {
            return $default;
        }

        return null;
    }

    private function resolveIds(BlockInterface $block)
    {
        $block->setSetting(
            'collectionId',
            \is_object($block->getSetting('collectionId')) ? $block->getSetting('collectionId')->getId() : null
        );
    }
}
