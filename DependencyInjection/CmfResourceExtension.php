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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CmfResourceExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('resource.xml');

        $config = $processor->processConfiguration($configuration, $configs);

        $this->loadRepositories($config['repository'], $container);
    }

    private function loadRepositories($config, $container)
    {
        foreach (array(
            'composite' => 'createCompositeRepository',
            'doctrine_phpcr' => 'createDoctrinePhpcrRepository',
            'doctrine_phpcr_odm' => 'createDoctrinePhpcrOdmRepository',
        ) as $type => $createMethod) {
            foreach ($config[$type] as $repoName => $repoConfig) {
                $definition = $this->$createMethod($repoConfig);
                $definition->addTag('cmf_resource.repository', array('type' => $type, 'name' => $repoName));
                $container->setDefinition('cmf_resource.repository.' . $type . '.' . $repoName, $definition);
            }
        }

        $container->setAlias('cmf_resource.registry', 'cmf_resource.registry.container');
    }

    private function createDoctrinePhpcrOdmRepository($config)
    {
        $definition = new Definition('Symfony\Cmf\Component\Resource\Repository\PhpcrOdmRepository');
        $definition->addArgument(new Reference('doctrine_phpcr'));
        $definition->addArgument($config['basepath']);

        return $definition;
    }

    private function createDoctrinePhpcrRepository($config)
    {
        $definition = new Definition('Symfony\Cmf\Component\Resource\Repository\PhpcrRepository');
        $definition->addArgument(new Reference('doctrine_phpcr.session'));
        $definition->addArgument($config['basepath']);

        return $definition;
    }

    private function createCompositeRepository($config)
    {
        $definition = new Definition('Puli\Repository\CompositeRepository');

        foreach ($config['mounts'] as $mount) {
            $definition->addMethodCall('mount', array($mount['mountpoint'], $mount['repository']));
        }

        return $definition;
    }

    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/' . $this->getAlias();
    }
}
