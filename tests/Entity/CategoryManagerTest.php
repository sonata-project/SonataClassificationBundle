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
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Tests\App\Entity\Category;
use Sonata\ClassificationBundle\Tests\App\Entity\Context;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryManagerTest extends KernelTestCase
{
    /**
     * TODO: Manual boot the kernel is not needed when dropping support for Symfony 4.
     */
    protected function setUp(): void
    {
        if (!self::$booted) {
            static::bootKernel();
        }
    }

    public function testGetRootCategoryWithChildren(): void
    {
        $rootCategory = $this->prepareData();

        $rootCategory = $this->getCategoryManager()->getRootCategoryWithChildren($rootCategory);
        static::assertCount(1, $rootCategory->getChildren());
    }

    public function testGetRootCategoriesForContext(): void
    {
        $rootCategory = $this->prepareData();

        $categories = $this->getCategoryManager()->getRootCategoriesForContext(
            $rootCategory->getContext()
        );

        static::assertCount(1, $categories);
    }

    public function testGetRootCategoriesSplitByContexts(): void
    {
        $this->prepareData();

        $categories = $this->getCategoryManager()->getRootCategoriesSplitByContexts(false);

        static::assertCount(2, $categories);
        static::assertArrayHasKey(1, $categories);
        static::assertArrayHasKey(2, $categories);
    }

    public function testGetBySlug(): void
    {
        $this->prepareData();

        $category = $this->getCategoryManager()->getBySlug('bar', '1', true);

        static::assertNotNull($category);
    }

    /**
     * @psalm-suppress UndefinedPropertyFetch
     */
    private function prepareData(): CategoryInterface
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        // @phpstan-ignore-next-line
        $container = method_exists($this, 'getContainer') ? self::getContainer() : self::$container;
        $manager = $container->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $context = new Context();
        $context->setId('1');
        $context->setName('default');
        $context->setEnabled(true);

        $anotherContext = new Context();
        $anotherContext->setId('2');
        $anotherContext->setName('bar');
        $anotherContext->setEnabled(true);

        $children = new Category();
        $children->setName('bar');
        $children->setContext($context);
        $children->setEnabled(true);

        $rootCategory = new Category();
        $rootCategory->setName('foo');
        $rootCategory->setContext($context);
        $rootCategory->addChild($children);
        $rootCategory->setEnabled(true);

        $anotherRootCategory = new Category();
        $anotherRootCategory->setName('bar');
        $anotherRootCategory->setContext($anotherContext);
        $anotherRootCategory->setEnabled(true);

        $manager->persist($context);
        $manager->persist($anotherContext);
        $manager->persist($children);
        $manager->persist($rootCategory);
        $manager->persist($anotherRootCategory);

        $manager->flush();

        return $rootCategory;
    }

    /**
     * @psalm-suppress UndefinedPropertyFetch
     */
    private function getCategoryManager(): CategoryManagerInterface
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        // @phpstan-ignore-next-line
        $container = method_exists($this, 'getContainer') ? self::getContainer() : self::$container;
        $categoryManager = $container->get('sonata.classification.manager.category');
        \assert($categoryManager instanceof CategoryManagerInterface);

        return $categoryManager;
    }
}
