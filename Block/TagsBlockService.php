<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class TagsBlockService extends BaseBlockService
{
    /**
     * @var TagManagerInterface
     */
    protected $manager;

    /**
     * @param string              $name
     * @param EngineInterface     $templating
     * @param TagManagerInterface $manager
     */
    public function __construct($name, EngineInterface $templating, TagManagerInterface $manager)
    {
        $this->manager = $manager;

        parent::__construct($name, $templating);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $criteria = array(
            'enabled' => true,
            'context' => $blockContext->getSetting('context'),
        );

        $parameters = array(
            'context'    => $blockContext,
            'settings'   => $blockContext->getSettings(),
            'block'      => $blockContext->getBlock(),
            'pager'      => $this->manager->getPager($criteria, 1, $blockContext->getSetting('limit')),
        );

        return $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        $errorElement
            ->with('settings.limit')
                ->assertType(array(
                    'type'       => 'integer',
                    'acceptNull' => false,
                ))
            ->end()
            ->with('settings.context')
                ->assertNotBlank()
            ->end()
            ->with('settings.title')
                ->assertNotBlank()
                ->assertMaxLength(array('limit' => 50))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper
            ->add('settings', 'sonata_type_immutable_array', array(
                'keys' => array(
                    array('context', 'text', array('required' => false)),
                    array('limit', 'integer', array('required' => false)),
                    array('title', 'text', array('required' => false)),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Tags';
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'limit'      => null,
            'context'    => 'default',
            'title'      => 'Tags',
            'template'   => 'SonataClassificationBundle:Block:tags.html.twig',
        ));
    }
}
