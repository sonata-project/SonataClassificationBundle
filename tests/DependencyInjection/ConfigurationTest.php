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

namespace Sonata\ClassificationBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Sonata\ClassificationBundle\Admin\CategoryAdmin;
use Sonata\ClassificationBundle\Admin\CollectionAdmin;
use Sonata\ClassificationBundle\Admin\ContextAdmin;
use Sonata\ClassificationBundle\Admin\TagAdmin;
use Sonata\ClassificationBundle\DependencyInjection\Configuration;
use Sonata\ClassificationBundle\DependencyInjection\SonataClassificationExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    public function testDefault(): void
    {
        $this->assertProcessedConfigurationEquals([
            'class' => [
                'category' => 'Application\Sonata\ClassificationBundle\Entity\Category',
                'tag' => 'Application\Sonata\ClassificationBundle\Entity\Tag',
                'collection' => 'Application\Sonata\ClassificationBundle\Entity\Collection',
                'context' => 'Application\Sonata\ClassificationBundle\Entity\Context',
            ],
            'admin' => [
                'category' => [
                    'class' => CategoryAdmin::class,
                    'controller' => 'SonataClassificationBundle:CategoryAdmin',
                    'translation' => 'SonataClassificationBundle',
                ],
                'tag' => [
                    'class' => TagAdmin::class,
                    'controller' => 'SonataAdminBundle:CRUD',
                    'translation' => 'SonataClassificationBundle',
                ],
                'collection' => [
                    'class' => CollectionAdmin::class,
                    'controller' => 'SonataAdminBundle:CRUD',
                    'translation' => 'SonataClassificationBundle',
                ],
                'context' => [
                    'class' => ContextAdmin::class,
                    'controller' => 'SonataAdminBundle:CRUD',
                    'translation' => 'SonataClassificationBundle',
                ],
            ],
        ], [
            __DIR__.'/../Fixtures/configuration.yaml',
        ]);
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new SonataClassificationExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
