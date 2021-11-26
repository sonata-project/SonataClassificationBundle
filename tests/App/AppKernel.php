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
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\ClassificationBundle\SonataClassificationBundle;
use Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\Twig\Bridge\Symfony\SonataTwigBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;

final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new SonataBlockBundle(),
            new SonataTwigBundle(),
            new SonataAdminBundle(),
            new KnpMenuBundle(),
            new SonataDoctrineORMAdminBundle(),
            new SonataClassificationBundle(),
            new DoctrineBundle(),
            new SonataDoctrineBundle(),
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

    /**
     * TODO: Add typehint when support for Symfony < 5.1 is dropped.
     *
     * @param RoutingConfigurator $routes
     */
    protected function configureRoutes($routes): void
    {
        $routes->import(__DIR__.'/Resources/config/routing/routes.yaml');
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/Resources/config/config.yaml');

        if (class_exists(AuthenticatorManager::class)) {
            $loader->load(__DIR__.'/Resources/config/config_symfony_v5.yaml');
        } else {
            $loader->load(__DIR__.'/Resources/config/config_symfony_v4.yaml');
        }

        $container->setParameter('app.base_dir', $this->getBaseDir());
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir().'/sonata-classification-bundle/var/';
    }
}
