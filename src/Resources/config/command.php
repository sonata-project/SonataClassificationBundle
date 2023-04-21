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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sonata\ClassificationBundle\Command\FixContextCommand;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set(FixContextCommand::class)
            ->public()
            ->tag('console.command')
            ->args([
                service('sonata.classification.manager.context'),
                service('sonata.classification.manager.tag'),
                service('sonata.classification.manager.collection'),
                service('sonata.classification.manager.category'),
            ]);
};
