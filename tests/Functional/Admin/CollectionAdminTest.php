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

namespace Sonata\ClassificationBundle\Tests\Functional\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\ClassificationBundle\Tests\App\Entity\Collection;
use Sonata\ClassificationBundle\Tests\App\Entity\Context;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CollectionAdminTest extends WebTestCase
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
     * @return iterable<array<string>>
     *
     * @phpstan-return iterable<array{string}>
     */
    public static function provideCrudUrlsCases(): iterable
    {
        yield 'List Collection' => ['/admin/tests/app/collection/list'];
        yield 'Create Collection' => ['/admin/tests/app/collection/create'];
        yield 'Edit Collection' => ['/admin/tests/app/collection/1/edit'];
        yield 'Remove Collection' => ['/admin/tests/app/collection/1/delete'];
    }

    /**
     * @dataProvider provideFormUrlsCases
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $fieldValues
     */
    public function testFormsUrls(string $url, array $parameters, string $button, array $fieldValues = []): void
    {
        $client = self::createClient();

        $this->prepareData();

        $client->request('GET', $url, $parameters);
        $client->submitForm($button, $fieldValues);
        $client->followRedirect();

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<array<string|array<string, mixed>>>
     *
     * @phpstan-return iterable<array{0: string, 1: array<string, mixed>, 2: string, 3?: array<string, mixed>}>
     */
    public static function provideFormUrlsCases(): iterable
    {
        yield 'Create Collection' => ['/admin/tests/app/collection/create', [
            'uniqid' => 'collection',
        ], 'btn_create_and_list', [
            'collection[name]' => 'Name',
            'collection[context]' => 'default',
        ]];

        yield 'Edit Collection' => ['/admin/tests/app/collection/1/edit', [], 'btn_update_and_list'];
        yield 'Remove Collection' => ['/admin/tests/app/collection/1/delete', [], 'btn_delete'];
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
        $context->setId('default');
        $context->setName('default');

        $collection = new Collection();
        $collection->setName('collection');
        $collection->setContext($context);

        $manager->persist($collection);

        $manager->flush();
    }
}
