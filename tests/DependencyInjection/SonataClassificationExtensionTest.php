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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\ClassificationBundle\DependencyInjection\SonataClassificationExtension;
use Sonata\Doctrine\Mapper\DoctrineCollector;

final class SonataClassificationExtensionTest extends AbstractExtensionTestCase
{
    public function testThereIsNoDoctrineMappingRegistrationWithoutSonataDoctrineBundle(): void
    {
        $this->container->setParameter('kernel.bundles', ['SonataAdminBundle' => true]);

        $this->load([
            'class' => [
                'tag' => \stdClass::class,
                'category' => \stdClass::class,
                'collection' => \stdClass::class,
                'context' => \stdClass::class,
            ],
        ]);

        $doctrineCollector = DoctrineCollector::getInstance();

        $this->assertCount(0, $doctrineCollector->getAssociations());
    }

    public function testDoctrineMappingRegistrationWhenSonataDoctrineBundleIsEnabled(): void
    {
        $this->container->setParameter('kernel.bundles', [
            'SonataAdminBundle' => true,
            'SonataDoctrineBundle' => true,
        ]);

        $this->load([
            'class' => [
                'tag' => \stdClass::class,
                'category' => \stdClass::class,
                'collection' => \stdClass::class,
                'context' => \stdClass::class,
            ],
        ]);

        $doctrineCollector = DoctrineCollector::getInstance();

        $this->assertTrue($doctrineCollector->getAssociations() > 0);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SonataClassificationExtension(),
        ];
    }
}
