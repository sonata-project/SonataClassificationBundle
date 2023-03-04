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

namespace Sonata\ClassificationBundle\Tests\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Tests\App\Entity\Collection;
use Sonata\ClassificationBundle\Tests\App\Entity\Context;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CollectionManagerTest extends KernelTestCase
{
    public function testGetBySlug(): void
    {
        $this->prepareData();

        $collection = $this->getCollectionManager()->getBySlug('collection', '1', false);

        static::assertNotNull($collection);
    }

    public function testGetByContext(): void
    {
        $this->prepareData();

        $collection = $this->getCollectionManager()->getByContext('1', false);

        static::assertCount(1, $collection);
    }

    /**
     * @psalm-suppress UndefinedPropertyFetch
     */
    private function prepareData(): void
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        // @phpstan-ignore-next-line
        $container = method_exists($this, 'getContainer') ? self::getContainer() : self::$container;
        $manager = $container->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $context = new Context();
        $context->setId('1');
        $context->setName('contextA');

        $collection = new Collection();
        $collection->setName('collection');
        $collection->setEnabled(false);
        $collection->setContext($context);

        $manager->persist($context);
        $manager->persist($collection);

        $manager->flush();
    }

    /**
     * @psalm-suppress UndefinedPropertyFetch
     */
    private function getCollectionManager(): CollectionManagerInterface
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        // @phpstan-ignore-next-line
        $container = method_exists($this, 'getContainer') ? self::getContainer() : self::$container;
        $collectionManager = $container->get('sonata.classification.manager.collection');
        \assert($collectionManager instanceof CollectionManagerInterface);

        return $collectionManager;
    }
}
