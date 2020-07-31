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

namespace Sonata\ClassificationBundle\Tests\Functional\Routing;

use Sonata\ClassificationBundle\Tests\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class RoutingTest extends WebTestCase
{
    /**
     * @group legacy
     *
     * @dataProvider getRoutes
     */
    public function testRoutes(string $name, string $path, array $methods): void
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');

        $route = $router->getRouteCollection()->get($name);

        $this->assertNotNull($route);
        $this->assertSame($path, $route->getPath());
        $this->assertEmpty(array_diff($methods, $route->getMethods()));

        $matchingPath = $path;
        $matchingFormat = '';
        if (false !== strpos($matchingPath, '.{_format}', -10)) {
            $matchingFormat = '.json';
            $matchingPath = str_replace('.{_format}', $matchingFormat, $path);
        }

        $matcher = $router->getMatcher();
        $requestContext = $router->getContext();

        foreach ($methods as $method) {
            $requestContext->setMethod($method);

            // Check paths like "/api/classification/categories.json".
            $match = $matcher->match($matchingPath);

            $this->assertSame($name, $match['_route']);

            if ($matchingFormat) {
                $this->assertSame(ltrim($matchingFormat, '.'), $match['_format']);
            }

            $matchingPathWithStrippedFormat = str_replace('.{_format}', '', $path);

            // Check paths like "/api/classification/categories".
            $match = $matcher->match($matchingPathWithStrippedFormat);

            $this->assertSame($name, $match['_route']);

            if ($matchingFormat) {
                $this->assertSame(ltrim($matchingFormat, '.'), $match['_format']);
            }
        }
    }

    public function getRoutes(): iterable
    {
        // API
        yield ['nelmio_api_doc_index', '/api/doc/{view}', ['GET']];

        // API - category
        yield ['sonata_api_classification_category_get_categories', '/api/classification/categories.{_format}', ['GET']];
        yield ['sonata_api_classification_category_get_category', '/api/classification/categories/{id}.{_format}', ['GET']];
        yield ['sonata_api_classification_category_post_category', '/api/classification/categories.{_format}', ['POST']];
        yield ['sonata_api_classification_category_put_category', '/api/classification/categories/{id}.{_format}', ['PUT']];
        yield ['sonata_api_classification_category_delete_category', '/api/classification/categories/{id}.{_format}', ['DELETE']];

        // API - collection
        yield ['sonata_api_classification_collection_get_collections', '/api/classification/collections.{_format}', ['GET']];
        yield ['sonata_api_classification_collection_get_collection', '/api/classification/collections/{id}.{_format}', ['GET']];
        yield ['sonata_api_classification_collection_post_collection', '/api/classification/collections.{_format}', ['POST']];
        yield ['sonata_api_classification_collection_put_collection', '/api/classification/collections/{id}.{_format}', ['PUT']];
        yield ['sonata_api_classification_collection_delete_collection', '/api/classification/collections/{id}.{_format}', ['DELETE']];

        // API - tag
        yield ['sonata_api_classification_tag_get_tags', '/api/classification/tags.{_format}', ['GET']];
        yield ['sonata_api_classification_tag_get_tag', '/api/classification/tags/{id}.{_format}', ['GET']];
        yield ['sonata_api_classification_tag_post_tag', '/api/classification/tags.{_format}', ['POST']];
        yield ['sonata_api_classification_tag_put_tag', '/api/classification/tags/{id}.{_format}', ['PUT']];
        yield ['sonata_api_classification_tag_delete_tag', '/api/classification/tags/{id}.{_format}', ['DELETE']];

        // API - context
        yield ['sonata_api_classification_context_get_contexts', '/api/classification/contexts.{_format}', ['GET']];
        yield ['sonata_api_classification_context_get_context', '/api/classification/contexts/{id}.{_format}', ['GET']];
        yield ['sonata_api_classification_context_post_context', '/api/classification/contexts.{_format}', ['POST']];
        yield ['sonata_api_classification_context_put_context', '/api/classification/contexts/{id}.{_format}', ['PUT']];
        yield ['sonata_api_classification_context_delete_context', '/api/classification/contexts/{id}.{_format}', ['DELETE']];
    }

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
