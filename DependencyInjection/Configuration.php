<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    /**
     * Returns the config tree builder.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('cmf_resource')
            ->children()
                ->arrayNode('repository')
                    ->children()
                        ->arrayNode('doctrine_phpcr_odm')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('basepath')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('doctrine_phpcr')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('basepath')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('composite')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->arrayNode('mounts')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('repository')->end()
                                                ->scalarNode('mountpoint')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
