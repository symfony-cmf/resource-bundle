<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
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
            ->fixXmlConfig('repository', 'repositories')
            ->children()
                ->arrayNode('description')
                    ->addDefaultsIfNotSet()
                    ->fixXmlConfig('enhancer')
                    ->children()
                        ->arrayNode('enhancers')
                            ->example('sonata_admin')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('repositories')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->beforeNormalization()
                            ->ifTrue(function ($n) {
                                return is_array($n) && !isset($n['options']) && !isset($n['option']);
                            })
                            ->then(function ($n) {
                                $options = [];

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
                                                $mountConfig = [];
                                                foreach ($mount['option'] as $option) {
                                                    $mountConfig[$option['name']] = $option['value'];
                                                }

                                                $n[$id][$mountId] = $mountConfig;
                                            }

                                            unset($n[$id]['collection']);
                                        }

                                        return $n;
                                    })
                                ->end()
                                ->useAttributeAsKey('name')
                                ->defaultValue([])
                                ->prototype('variable')->end()
                            ->end() // options
                        ->end()
                    ->end()
                ->end() // repositories
                ->scalarNode('default_repository')->defaultValue('default')->end()
            ->end();

        return $treeBuilder;
    }
}
