<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class RepositoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(
            'cmf_resource.repository.factory.container'
        )) {
            return;
        }

        $repositoryFactory = $container->getDefinition(
            'cmf_resource.repository.factory.container'
        );

        $ids = $container->findTaggedServiceIds('cmf_resource.repository');
        $map = array();

        foreach ($ids as $id => $attributes) {
            if (!isset($attributes[0]['name'])) {
                throw new \InvalidArgumentException(sprintf(
                    'No "name" attribute specified for repository service definition tag: "%s"',
                    $id
                ));
            }

            $map[$attributes[0]['name']] = $id;
        }

        $repositoryFactory->replaceArgument(1, $map);
    }
}
