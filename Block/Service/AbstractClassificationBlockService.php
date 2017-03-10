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

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Christian Gripp <mail@core23.de>
 */
abstract class AbstractClassificationBlockService extends AbstractAdminBlockService
{
    /**
     * @var ContextManagerInterface
     */
    protected $contextManager;

    /**
     * @param string                  $name
     * @param EngineInterface         $templating
     * @param ContextManagerInterface $contextManager
     */
    public function __construct($name, EngineInterface $templating, ContextManagerInterface $contextManager)
    {
        parent::__construct($name, $templating);

        $this->contextManager = $contextManager;
    }

    /**
     * @param FormMapper     $formMapper
     * @param AdminInterface $admin
     * @param string         $formField
     * @param string         $field
     * @param array          $fieldOptions
     * @param array          $adminOptions
     *
     * @return FormBuilder
     */
    final protected function getFormAdminType(FormMapper $formMapper, AdminInterface $admin, $formField, $field, $fieldOptions = array(), $adminOptions = array())
    {
        $adminOptions = array_merge(array(
            'edit' => 'list',
            'translation_domain' => 'SonataClassificationBundle',
        ), $adminOptions);

        $fieldDescription = $admin->getModelManager()->getNewFieldDescriptionInstance($admin->getClass(), $field, $adminOptions);
        $fieldDescription->setAssociationAdmin($admin);
        $fieldDescription->setAdmin($formMapper->getAdmin());
        $fieldDescription->setAssociationMapping(array(
            'fieldName' => $field,
            'type' => ClassMetadataInfo::MANY_TO_ONE,
        ));

        $fieldOptions = array_merge(array(
            'sonata_field_description' => $fieldDescription,
            'class' => $admin->getClass(),
            'model_manager' => $admin->getModelManager(),
            'required' => false,
        ), $fieldOptions);

        return $formMapper->create($formField, 'sonata_type_model_list', $fieldOptions);
    }

    /**
     * Returns an context choice array.
     *
     * @return string[]
     */
    final protected function getContextChoices()
    {
        $contextChoices = array();
        /* @var ContextInterface $context */
        foreach ($this->contextManager->findAll() as $context) {
            $contextChoices[$context->getId()] = $context->getName();
        }

        return $contextChoices;
    }
}
