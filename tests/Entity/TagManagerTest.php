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

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Entity\BaseTag;
use Sonata\ClassificationBundle\Entity\TagManager;
use Sonata\Doctrine\Test\EntityManagerMockFactoryTrait;

final class TagManagerTest extends TestCase
{
    use EntityManagerMockFactoryTrait;

    public function testGetBySlug(): void
    {
        $self = $this;
        $this
            ->getTagManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->exactly(3))->method('andWhere')->withConsecutive(
                    [$self->equalTo('t.slug = :slug')],
                    [$self->equalTo('t.context = :context')],
                    [$self->equalTo('t.enabled = :enabled')]
                )->willReturn($qb);
                $qb->expects($self->exactly(3))->method('setParameter')->withConsecutive(
                    [$self->equalTo('slug'), $self->equalTo('theslug')],
                    [$self->equalTo('context'), $self->equalTo('contextA')],
                    [$self->equalTo('enabled'), $self->equalTo(false)]
                )->willReturn($qb);
            })
            ->getBySlug('theslug', 'contextA', false);
    }

    public function testGetByContext(): void
    {
        $self = $this;
        $this
            ->getTagManager(static function (MockObject $qb) use ($self): void {
                $qb->expects($self->exactly(2))->method('andWhere')->withConsecutive(
                    [$self->equalTo('t.context = :context')],
                    [$self->equalTo('t.enabled = :enabled')]
                )->willReturn($qb);
                $qb->expects($self->exactly(2))->method('setParameter')->withConsecutive(
                    [$self->equalTo('context'), $self->equalTo('contextA')],
                    [$self->equalTo('enabled'), $self->equalTo(false)]
                )->willReturn($qb);
            })
            ->getByContext('contextA', false);
    }

    private function getTagManager($qbCallback): TagManager
    {
        $em = $this->createEntityManagerMock($qbCallback, []);

        $registry = $this->getMockForAbstractClass(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($em);

        return new TagManager(BaseTag::class, $registry);
    }
}
