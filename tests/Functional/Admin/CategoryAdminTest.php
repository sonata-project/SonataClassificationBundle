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
use Sonata\ClassificationBundle\Tests\App\Entity\Category;
use Sonata\ClassificationBundle\Tests\App\Entity\Context;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CategoryAdminTest extends WebTestCase
{
    /**
     * @param array<string, mixed> $parameters
     *
     * @dataProvider provideCrudUrlsCases
     */
    public function testCrudUrls(string $url, array $parameters = []): void
    {
        $client = self::createClient();

        $this->prepareData();

        dump($client->request('GET', $url, $parameters));

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<array<string|array<string, mixed>>>
     *
     * @phpstan-return iterable<array{0: string, 1?: array<string, mixed>}>
     */
    public static function provideCrudUrlsCases(): iterable
    {
        /*yield 'List Category' => ['/admin/tests/app/category/list', [
            'filter' => [
                'context' => [
                    'value' => 'default',
                ],
            ],
        ]];*/
        //yield 'Tree Category' => ['/admin/tests/app/category/tree'];
        yield 'Tree Category default context' => ['/admin/tests/app/category/tree', [
            'context' => 'default',
        ]];
        //yield 'Create Category' => ['/admin/tests/app/category/create'];
        //yield 'Edit Category' => ['/admin/tests/app/category/1/edit'];
        //yield 'Remove Category' => ['/admin/tests/app/category/1/delete'];
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
        yield 'Create Category' => ['/admin/tests/app/category/create', [
            'uniqid' => 'category',
        ], 'btn_create_and_list', [
            'category[name]' => 'Name',
        ]];

        yield 'Edit Category' => ['/admin/tests/app/category/1/edit', [], 'btn_update_and_list'];
        yield 'Remove Category' => ['/admin/tests/app/category/1/delete', [], 'btn_delete'];
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

        $category = new Category();
        $category->setName('category');
        $category->setContext($context);

        $manager->persist($context);
        $manager->persist($category);

        $manager->flush();
    }
}
