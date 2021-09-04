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

namespace Sonata\ClassificationBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FOS\RestBundle\FOSRestBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\NelmioApiDocBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\ClassificationBundle\SonataClassificationBundle;
use Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new FOSRestBundle(),
            new SonataAdminBundle(),
            new KnpMenuBundle(),
            new SonataDoctrineORMAdminBundle(),
            new SonataClassificationBundle(),
            new JMSSerializerBundle(),
            new DoctrineBundle(),
            new NelmioApiDocBundle(),
            new SonataDoctrineBundle(),
            new SonataAdminBundle(),
            new SonataDoctrineORMAdminBundle(),
            new KnpMenuBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir().'cache';
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir().'log';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        if (class_exists(Operation::class)) {
            $routes->import(__DIR__.'/Resources/config/routing/api_nelmio_v3.yml', '/', 'yaml');
        } else {
            $routes->import(__DIR__.'/Resources/config/routing/api_nelmio_v2.yml', '/', 'yaml');
        }
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/Resources/config/config.yml');
        $loader->load(__DIR__.'/Resources/config/security.yml');
        $containerBuilder->setParameter('app.base_dir', $this->getBaseDir());
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir().'/sonata-classification-bundle/var/';
    }
}
