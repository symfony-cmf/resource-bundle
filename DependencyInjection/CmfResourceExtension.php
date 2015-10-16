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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
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

        $this->loadRepositories($config['repositories'], $container);
    }

    private function loadRepositories(array $repositories, ContainerBuilder $container)
    {
        $methods = array(
            'composite' => 'createCompositeRepository',
            'doctrine_phpcr' => 'createDoctrinePhpcrRepository',
            'doctrine_phpcr_odm' => 'createDoctrinePhpcrOdmRepository',
            'filesystem' => 'createFilesystemRepository',
        );
       
        foreach ($repositories as $alias => $repository) {
            if (!isset($methods[$repository['type']])) {
                throw new InvalidConfigurationException(sprintf(
                    'Unexpected repository type "%s", known types: %s',
                    $repository['type'],
                    implode(', ', array_keys($methods))
                ));
            }

            $definition = $this->{$methods[$repository['type']]}($repository['options'], $alias);
            $definition->addTag('cmf_resource.repository', array('type' => $repository['type'], 'alias' => $alias));
            $container->setDefinition('cmf_resource.repository.' . $alias, $definition);
        }

        $container->setAlias('cmf_resource.registry', 'cmf_resource.registry.container');
    }

    private function createDoctrinePhpcrOdmRepository(array $options, $alias)
    {
        if (!isset($options['basepath'])) {
            $options['basepath'] = null;
        }

        $definition = new Definition('Symfony\Cmf\Component\Resource\Repository\PhpcrOdmRepository');
        $definition->addArgument(new Reference('doctrine_phpcr'));
        $definition->addArgument($options['basepath']);

        unset($options['basepath']);

        $this->validateRemainingOptions($options, array('basepath'), $alias);

        return $definition;
    }

    private function createDoctrinePhpcrRepository(array $options, $alias)
    {
        if (!isset($options['basepath'])) {
            $options['basepath'] = null;
        }

        $definition = new Definition('Symfony\Cmf\Component\Resource\Repository\PhpcrRepository');
        $definition->addArgument(new Reference('doctrine_phpcr.session'));
        $definition->addArgument($options['basepath']);

        unset($options['basepath']);

        $this->validateRemainingOptions($options, array('basepath'), $alias);

        return $definition;
    }

    private function createCompositeRepository(array $options, $alias)
    {
        if (!isset($options['mounts'])) {
            throw new InvalidConfigurationException('The composite repository type requires a "mounts" option to be set.');
        }

        $definition = new Definition('Puli\Repository\CompositeRepository');

        foreach ($options['mounts'] as $mount) {
            if (!isset($mount['mountpoint']) || !isset($mount['repository'])) {
                throw new InvalidConfigurationException('The "mounts" option of the composite repository type requires a "mountpoint" and "repository" options to be set.');
            }

            $definition->addMethodCall('mount', array($mount['mountpoint'], $mount['repository']));
        }

        unset($options['mounts']);

        $this->validateRemainingOptions($options, array('mounts'), $alias);

        return $definition;
    }

    private function createFilesystemRepository(array $options)
    {
        if (!isset($options['base_dir'])) {
            throw new InvalidConfigurationException('The filesystem repository type requires a "base_dir" option to be set.');
        }

        if (!isset($options['symlink'])) {
            $options['symlink'] = true;
        }

        $definition = new Definition('Puli\Repository\FilesystemRepository');
        $definition->setArguments(array($options['base_dir'], $options['symlink']));

        return $definition;
    }

    private function validateRemainingOptions(array $options, array $knownOptions, $name)
    {
        if (0 !== count($options)) {
            throw new InvalidConfigurationException(sprintf(
                'Unknown option configured for "%s": "%s". Known options: %s',
                $name,
                implode('", "', array_keys($options)),
                implode(', ', $knownOptions)
            ));
        }
    }

    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/' . $this->getAlias();
    }
}
