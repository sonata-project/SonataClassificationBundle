<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project <https://github.com/sonata-project/SonataClassificationBundle/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\ClassificationBundle\Entity\ContextManager;
use Sonata\ClassificationBundle\Model\ContextInterface;

abstract class ContextAwareAdmin extends Admin
{
    /**
     * @var ContextManager
     */
    protected $contextManager;

    /**
     * @return ContextManager
     */
    public function getContextManager()
    {
        return $this->contextManager;
    }

    /**
     * @param ContextManager $contextManager
     */
    public function setContextManager(ContextManager $contextManager)
    {
        $this->contextManager = $contextManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();

        if ($contextId = $this->getPersistentParameter('context')) {
            $context = $this->getContextManager()->find($contextId);

            if (!$context instanceof ContextInterface) {
                $context = $this->getContextManager()->create();
                $context->setEnabled(true);
                $context->setId($context);
                $context->setName($context);

                $this->getContextManager()->save($context);
            }

            $instance->setContext($context);
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        $parameters = array(
            'context'      => '',
            'hide_context' => $this->hasRequest() ? (int)$this->getRequest()->get('hide_context', 0) : 0
        );

        if ($this->hasSubject()) {
            $parameters['context'] = $this->getSubject()->getContext() ? $this->getSubject()->getContext()->getId() : '';

            return $parameters;
        }

        if ($this->hasRequest()) {
            $parameters['context'] = $this->getRequest()->get('context');
        }

        return $parameters;
    }
}