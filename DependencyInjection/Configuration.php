<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
        $root = $treeBuilder->root('cmf_resource');

        $this->addRepositoriesSection($root);
        $this->addDiscoverySection($root);

        return $treeBuilder;
    }

    public function addRepositoriesSection(ArrayNodeDefinition $root)
    {
        $root
            ->fixXmlConfig('repository', 'repositories')
            ->children()
                ->arrayNode('repositories')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->beforeNormalization()
                            ->ifTrue(function ($n) { return is_array($n) && !isset($n['options']) && !isset($n['option']); })
                            ->then(function ($n) {
                                $options = array();

                                foreach ($n as $name => $value) {
                                    if ('type' === $name) {
                                        continue;
                                    }

                                    $options[$name] = $value;
                                    unset($n[$name]);
                                }

                                $n['options'] = $options;

                                return $n;
                            })
                        ->end()
                        ->fixXmlConfig('option')
                        ->children()
                            ->scalarNode('type')->isRequired()->end()
                            ->arrayNode('options')
                                ->beforeNormalization()
                                    ->ifTrue(function ($n) {
                                        return is_array($n) && 0 !== count(array_filter($n, function ($i) {
                                            return isset($i['collection']);
                                        }));
                                    })
                                    ->then(function ($n) {
                                        foreach ($n as $id => $item) {
                                            if (!is_array($item) || !isset($item['collection'])) {
                                                continue;
                                            }

                                            foreach ($item['collection'] as $mountId => $mount) {
                                                $mountConfig = array();
                                                foreach ($mount['option'] as $option) {
                                                    $mountConfig[$option['name']] =  $option['value'];
                                                }

                                                $n[$id][$mountId] = $mountConfig;
                                            }

                                            unset($n[$id]['collection']);
                                        }

                                        return $n;
                                    })
                                ->end()
                                ->useAttributeAsKey('name')
                                ->defaultValue(array())
                                ->prototype('variable')->end()
                            ->end() // options
                        ->end()
                    ->end()
                ->end() // repositories
            ->end();
    }

    public function addDiscoverySection(ArrayNodeDefinition $root)
    {
        $root
            ->children()
                ->arrayNode('discovery')
                    ->canBeEnabled()
                    ->fixXmlConfig('type')
                    ->fixXmlConfig('binding')
                    ->children()
                        ->arrayNode('types')
                            ->prototype('scalar')->end()
                        ->end() // types
                        ->arrayNode('bindings')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('path')->isRequired()->end()
                                    ->scalarNode('type')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end() // bindings
                    ->end()
                ->end() // discovery
            ->end();
    }
}
