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

        foreach ($taggedIds as $serviceId => $attributes) {
            if (!isset($attributes[0]['alias'])) {
                throw new InvalidArgumentException(sprintf(
                    'Resource enhancer "%s" has no "alias" attribute in its tag',
                    $serviceId
                ));
            }

            $name = $attributes[0]['alias'];

            if (isset($enhancers[$name])) {
                throw new InvalidArgumentException(sprintf(
                    'Enhancer with name "%s" (serviceId: "%s") has already been registered',
                    $name,
                    $serviceId
                ));
            }

            $enhancers[$name] = new Reference($serviceId);
        }

        $enhancerNames = array_keys($enhancers);
        $diff = array_diff($enabledEnhancers, $enhancerNames);

        if ($diff) {
            throw new InvalidArgumentException(sprintf(
                'Unknown description enhancer(s) "%s", available enhancers: "%s"',
                implode('", "', $diff),
                implode('", "', $enhancerNames)
            ));
        }

        $inactiveEnhancers = array_diff($enhancerNames, $enabledEnhancers);
        foreach ($inactiveEnhancers as $inactiveEnhancer) {
            $container->removeDefinition((string) $enhancers[$inactiveEnhancer]);
            unset($enhancers[$inactiveEnhancer]);
        }

        $registryDef = $container->getDefinition('cmf_resource.description.factory');
        $registryDef->replaceArgument(0, array_values($enhancers));
    }
}
