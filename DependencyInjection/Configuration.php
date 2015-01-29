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
                    ->addDefaultsIfNotSet()
                    ->fixXmlConfig('composite_repository', 'composite')
                    ->fixXmlConfig('doctrine_phpcr_repository', 'doctrine_phpcr')
                    ->fixXmlConfig('doctrine_phpcr_odm_repository', 'doctrine_phpcr_odm')
                    ->children()
                        ->arrayNode('doctrine_phpcr_odm')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('basepath')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('doctrine_phpcr')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('basepath')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('filesystem')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('base_dir')->end()
                                    ->booleanNode('symlink')->defaultValue(true)->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('composite')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->fixXmlConfig('mount')
                                ->children()
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
