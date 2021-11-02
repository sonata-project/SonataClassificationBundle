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

namespace Sonata\ClassificationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\ClassificationBundle\Model\ContextAwareInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;

/**
 * @phpstan-template T of ContextAwareInterface
 * @phpstan-extends AbstractAdmin<T>
 */
abstract class ContextAwareAdmin extends AbstractAdmin
{
    protected ContextManagerInterface $contextManager;

    /**
     * @phpstan-param class-string<T> $class
     */
    public function __construct(string $code, string $class, string $baseControllerName, ContextManagerInterface $contextManager)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->contextManager = $contextManager;
    }

    protected function alterNewInstance(object $object): void
    {
        $contextId = $this->getPersistentParameter('context', '');
        if ('' !== $contextId) {
            $context = $this->contextManager->find($contextId);

            if (null !== $context) {
                $context = $this->contextManager->create();
                $context->setEnabled(true);
                $context->setId($contextId);
                $context->setName($contextId);

                $this->contextManager->save($context);
            }

            $object->setContext($context);
        }
    }

    protected function configurePersistentParameters(): array
    {
        $parameters = [
            'context' => '',
            'hide_context' => $this->hasRequest() ? (int) $this->getRequest()->get('hide_context', 0) : 0,
        ];

        if ($this->hasSubject()) {
            $context = $this->getSubject()->getContext();

            $parameters['context'] = null !== $context ? $context->getId() : '';

            return $parameters;
        }

        if ($this->hasRequest()) {
            $parameters['context'] = $this->getRequest()->get('context');

            return $parameters;
        }

        return $parameters;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $options = [];

        if (1 === $this->getPersistentParameter('hide_context')) {
            $options['disabled'] = true;
        }

        $filter->add('context', null, [], $options);
    }
}
