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

namespace Sonata\ClassificationBundle\Tests\App\Action;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\ClassificationBundle\Tests\App\Entity\Context;
use Sonata\ClassificationBundle\Tests\App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TagAdminTest extends WebTestCase
{
    /**
     * @dataProvider provideCrudUrlsCases
     */
    public function testCrudUrls(string $url): void
    {
        $client = self::createClient();

        $this->prepareData();

        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<string[]>
     *
     * @phpstan-return \Generator<array{string}>
     */
    public static function provideCrudUrlsCases(): iterable
    {
        yield 'List' => ['/admin/tests/app/tag/list'];
        yield 'Create' => ['/admin/tests/app/tag/create'];
        yield 'Edit' => ['/admin/tests/app/tag/1/edit'];
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

        $context = $manager->find(Context::class, 'dummy');

        if (null === $context) {
            $context = new Context();
            $context->setId('dummy');
            $context->setName('Dummy context');

            $manager->persist($context);
        }

        $tag = new Tag();
        $tag->setName('My tag');
        $tag->setSlug(uniqid());
        $tag->setContext($context);

        $manager->persist($tag);

        $manager->flush();
    }
}
