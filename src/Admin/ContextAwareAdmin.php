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
     * NEXT_MAJOR: Change signature to (ContextManagerInterface).
     *
     * @param ContextManagerInterface|string $contextManager
     *
     * @phpstan-param class-string<T>|null $deprecatedClass
     */
    public function __construct(
        $contextManager,
        ?string $deprecatedClass = null,
        ?string $deprecatedBaseControllerName = null,
        ?ContextManagerInterface $deprecatedContextManager = null,
    ) {
        // NEXT_MAJOR: Keep the if part.
        if ($contextManager instanceof ContextManagerInterface) {
            parent::__construct();

            $this->contextManager = $contextManager;
        } else {
            \assert(\is_string($deprecatedClass));
            \assert(\is_string($deprecatedBaseControllerName));
            \assert(null !== $deprecatedContextManager);

            parent::__construct($contextManager, $deprecatedClass, $deprecatedBaseControllerName);

            $this->contextManager = $deprecatedContextManager;
        }
    }

    protected function alterNewInstance(object $object): void
    {
        $contextId = $this->getPersistentParameter('context', '');
        if ('' !== $contextId) {
            $context = $this->contextManager->find($contextId);

            if (null === $context) {
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
            'hide_context' => $this->hasRequest() ? $this->getRequest()->query->getInt('hide_context', 0) : 0,
        ];

        if ($this->hasSubject()) {
            $context = $this->getSubject()->getContext();

            $parameters['context'] = null !== $context ? $context->getId() : '';

            return $parameters;
        }

        if ($this->hasRequest()) {
            $parameters['context'] = $this->getRequest()->query->get('context');

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
