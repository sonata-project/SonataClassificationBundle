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
     * @return iterable<array<string>>
     *
     * @phpstan-return iterable<array{string}>
     */
    public static function provideCrudUrlsCases(): iterable
    {
        yield 'List Tag' => ['/admin/tests/app/tag/list'];
        yield 'Create Tag' => ['/admin/tests/app/tag/create'];
        yield 'Edit Tag' => ['/admin/tests/app/tag/1/edit'];
        yield 'Remove Tag' => ['/admin/tests/app/tag/1/delete'];
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
        yield 'Create Tag' => ['/admin/tests/app/tag/create', [
            'uniqid' => 'tag',
        ], 'btn_create_and_list', [
            'tag[name]' => 'Name',
            'tag[context]' => 'default',
        ]];

        yield 'Edit Tag' => ['/admin/tests/app/tag/1/edit', [], 'btn_update_and_list'];
        yield 'Remove Tag' => ['/admin/tests/app/tag/1/delete', [], 'btn_delete'];
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

        $tag = new Tag();
        $tag->setName('My tag');
        $tag->setContext($context);

        $manager->persist($tag);

        $manager->flush();
    }
}