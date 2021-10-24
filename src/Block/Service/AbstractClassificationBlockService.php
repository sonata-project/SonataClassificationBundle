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
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\ClassificationBundle\Model\ContextAwareInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Twig\Environment;

/**
 * @author Christian Gripp <mail@core23.de>
 */
abstract class AbstractClassificationBlockService extends AbstractBlockService
{
    /**
     * @var ContextManagerInterface
     */
    protected $contextManager;

    public function __construct(Environment $twig, ContextManagerInterface $contextManager)
    {
        parent::__construct($twig);

        $this->contextManager = $contextManager;
    }

    /**
     * @param string $formField
     * @param string $field
     * @param array  $fieldOptions
     * @param array  $adminOptions
     *
     * @phpstan-param AdminInterface<ContextAwareInterface> $admin
     */
    final protected function getFormAdminType(FormMapper $formMapper, AdminInterface $admin, $formField, $field, $fieldOptions = [], $adminOptions = []): FormBuilderInterface
    {
        $adminOptions = array_merge([
            'edit' => 'list',
            'translation_domain' => 'SonataClassificationBundle',
        ], $adminOptions);

        $fieldDescription = $admin->createFieldDescription($field, $adminOptions);
        $fieldDescription->setAssociationAdmin($admin);
        $fieldDescription->setAdmin($formMapper->getAdmin());

        $fieldOptions = array_merge([
            'sonata_field_description' => $fieldDescription,
            'class' => $admin->getClass(),
            'model_manager' => $admin->getModelManager(),
            'required' => false,
        ], $fieldOptions);

        return $formMapper->create($formField, ModelListType::class, $fieldOptions);
    }

    /**
     * Returns a context choice array.
     *
     * @return string[]
     */
    final protected function getContextChoices()
    {
        $contextChoices = [];
        /* @var ContextInterface $context */
        foreach ($this->contextManager->findAll() as $context) {
            $contextChoices[$context->getId()] = $context->getName();
        }

        return $contextChoices;
    }
}
