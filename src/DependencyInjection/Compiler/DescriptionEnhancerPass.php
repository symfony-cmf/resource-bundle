<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class DescriptionEnhancerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('cmf_resource.description.factory')) {
            return;
        }

        $taggedIds = $container->findTaggedServiceIds('cmf_resource.description.enhancer');
        $enabledEnhancers = $container->getParameter('cmf_resource.description.enabled_enhancers');
        $validAttributes = ['name', 'alias', 'priority'];

        foreach ($taggedIds as $serviceId => $attributes) {
            $attributes = $attributes[0];

            if ($diff = array_diff(array_keys($attributes), $validAttributes)) {
                throw new InvalidArgumentException(sprintf(
                    'Unknown tag attributes "%s" for service "%s", valid attributes: "%s"',
                    implode('", "', $diff), $serviceId, implode('", "', $validAttributes)
                ));
            }

            if (!isset($attributes['alias'])) {
                throw new InvalidArgumentException(sprintf(
                    'Resource enhancer "%s" has no "alias" attribute in its tag',
                    $serviceId
                ));
            }

            $name = $attributes['alias'];

            if (isset($enhancers[$name])) {
                throw new InvalidArgumentException(sprintf(
                    'Enhancer with name "%s" (serviceId: "%s") has already been registered',
                    $name,
                    $serviceId
                ));
            }

            $priority = isset($attributes['priority']) ? $attributes['priority'] : 0;
            $enhancers[$name] = [$priority, new Reference($serviceId)];
        }

        $enhancerNames = array_keys($enhancers);
        $diff = array_diff($enabledEnhancers, $enhancerNames);

        if ($diff) {
            throw new InvalidArgumentException(sprintf(
                'Unknown description enhancer(s) "%s" were enabled, available enhancers: "%s"',
                implode('", "', $diff),
                implode('", "', $enhancerNames)
            ));
        }

        $inactiveEnhancers = array_diff($enhancerNames, $enabledEnhancers);
        foreach ($inactiveEnhancers as $inactiveEnhancer) {
            unset($enhancers[$inactiveEnhancer]);
        }

        // sort enhancers, higher = more priority
        usort($enhancers, function ($a, $b) {
            return -strcmp($a[0], $b[0]);
        });

        $enhancers = array_map(function ($enhancer) {
            return $enhancer[1];
        }, $enhancers);

        $registryDef = $container->getDefinition('cmf_resource.description.factory');
        $registryDef->replaceArgument(0, array_values($enhancers));
    }
}
