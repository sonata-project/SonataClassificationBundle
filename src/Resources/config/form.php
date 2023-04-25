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

use Sonata\ClassificationBundle\Form\Type\CategorySelectorType;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.classification.form.type.category_selector', CategorySelectorType::class)
            ->public()
            ->tag('form.type', [
                'alias' => 'sonata_category_selector',
            ])
            ->args([
                service('sonata.classification.manager.category'),
            ]);
};
