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

namespace Sonata\ClassificationBundle\Command;

use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'sonata:classification:fix-context', description: 'Generate the default context if none defined and attach the context to all elements')]
final class FixContextCommand extends Command
{
    public function __construct(
        private ContextManagerInterface $contextManager,
        private TagManagerInterface $tagManager,
        private CollectionManagerInterface $collectionManager,
        private CategoryManagerInterface $categoryManager
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('1. Checking default context');

        $defaultContext = $this->contextManager->findOneBy([
            'id' => ContextInterface::DEFAULT_CONTEXT,
        ]);

        if (null === $defaultContext) {
            $output->writeln(' > default context is missing, creating one');
            $defaultContext = $this->contextManager->create();
            $defaultContext->setId(ContextInterface::DEFAULT_CONTEXT);
            $defaultContext->setName('Default');
            $defaultContext->setEnabled(true);

            $this->contextManager->save($defaultContext);
        } else {
            $output->writeln(' > default context exists');
        }

        $output->writeln('2. Find tag without default context');

        foreach ($this->tagManager->findBy([]) as $tag) {
            if (null !== $tag->getContext()) {
                continue;
            }

            $tagId = $tag->getId();
            \assert(null !== $tagId);

            $output->writeln(\sprintf(' > attach default context to tag: %s (%s)', $tag->getSlug() ?? '', $tagId));
            $tag->setContext($defaultContext);

            $this->tagManager->save($tag);
        }

        $output->writeln('3. Find collection without default context');

        foreach ($this->collectionManager->findBy([]) as $collection) {
            if (null !== $collection->getContext()) {
                continue;
            }

            $collectionId = $collection->getId();
            \assert(null !== $collectionId);

            $output->writeln(\sprintf(' > attach default context to collection: %s (%s)', $collection->getSlug() ?? '', $collectionId));
            $collection->setContext($defaultContext);

            $this->collectionManager->save($collection);
        }

        $output->writeln('3. Find category without default context');

        foreach ($this->categoryManager->findBy([]) as $category) {
            if (null !== $category->getContext()) {
                continue;
            }

            $categoryId = $category->getId();
            \assert(null !== $categoryId);

            $output->writeln(\sprintf(' > attach default context to collection: %s (%s)', $category->getSlug() ?? '', $categoryId));
            $category->setContext($defaultContext);

            $this->categoryManager->save($category);
        }

        $output->writeln('Done!');

        return 0;
    }
}
