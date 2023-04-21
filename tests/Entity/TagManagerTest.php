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
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\ClassificationBundle\Tests\App\Entity\Context;
use Sonata\ClassificationBundle\Tests\App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class TagManagerTest extends KernelTestCase
{
    public function testGetBySlug(): void
    {
        $this->prepareData();

        $tag = $this->getTagManager()->getBySlug('tag', '1', false);

        static::assertNotNull($tag);
    }

    public function testGetByContext(): void
    {
        $this->prepareData();

        $tag = $this->getTagManager()->getByContext('1', false);

        static::assertCount(1, $tag);
    }

    private function prepareData(): void
    {
        $manager = self::getContainer()->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $context = new Context();
        $context->setId('1');
        $context->setName('contextA');

        $collection = new Tag();
        $collection->setName('tag');
        $collection->setEnabled(false);
        $collection->setContext($context);

        $manager->persist($context);
        $manager->persist($collection);

        $manager->flush();
    }

    private function getTagManager(): TagManagerInterface
    {
        $tagManager = self::getContainer()->get('sonata.classification.manager.tag');
        \assert($tagManager instanceof TagManagerInterface);

        return $tagManager;
    }
}
