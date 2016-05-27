<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class RegistryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('cmf_resource.registry.container')) {
            return;
        }

        $repositoryRegistry = $container->getDefinition('cmf_resource.registry.container');

        $ids = $container->findTaggedServiceIds('cmf_resource.repository');
        $map = [];
        $types = [];

        foreach ($ids as $id => $attributes) {
            foreach (['alias', 'type'] as $requiredKey) {
                if (!isset($attributes[0][$requiredKey])) {
                    throw new \InvalidArgumentException(sprintf(
                        'No "%s" attribute specified for repository service definition tag: "%s"',
                        $requiredKey,
                        $id
                    ));
                }
            }

            $map[$attributes[0]['alias']] = $id;
            $types[$attributes[0]['type']] = $container->getParameterBag()->resolveValue(
                $container->getDefinition($id)->getClass()
            );
        }

        $repositoryRegistry->replaceArgument(1, $map);
        $repositoryRegistry->replaceArgument(2, $types);
    }
}
