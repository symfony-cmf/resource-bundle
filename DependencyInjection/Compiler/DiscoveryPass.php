<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class DiscoveryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('cmf_resource.discovery')) {
            return;
        }

        $discovery = $container->findDefinition('cmf_resource.discovery');

        $this->registerBindingTypes($container, $discovery);
        $this->registerBindings($container, $discovery);
    }

    private function registerBindings(ContainerBuilder $container, Definition $discovery)
    {
        if (!$container->hasParameter('cmf_resource.discovery.bindings')) {
            return;
        }

        $bindings = $container->getParameter('cmf_resource.discovery.bindings');
        foreach ($bindings as $binding) {
            $discovery->addMethodCall('addBinding', array(
                new Definition(
                    'Puli\Discovery\Binding\ResourceBinding',
                    array($binding['path'], $binding['type'])
                )
            ));
        }
    }

    private function registerBindingTypes(ContainerBuilder $container, Definition $discovery)
    {
        $types = $container->findTaggedServiceIds('cmf_resource.binding_type');
        foreach ($types as $id => $tags) {
            $discovery->addMethodCall('addBindingType', array(new Reference($id)));
        }
    }
}
