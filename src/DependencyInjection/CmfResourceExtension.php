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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory\RepositoryFactoryInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmfResourceExtension extends Extension
{
    private $repositoryFactories = [];

    /**
     * Return the full service ID for a given repository name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function getRepositoryServiceId($name)
    {
        return sprintf('cmf_resource.repository.%s', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('resource.xml');
        $loader->load('description.xml');
        $loader->load('twig.xml');
        $container->setParameter('cmf_resource.description.enabled_enhancers', $config['description']['enhancers']);

        $container->setParameter('cmf_resource.repositories.default_name', $config['default_repository']);
        $this->loadRepositories($container, $config['repositories'], $config['default_repository']);
    }

    private function loadRepositories(ContainerBuilder $container, array $configs, $defaultRepositoryName)
    {
        $repositoryTypes = array_keys($this->repositoryFactories);
        $typeMap = [];
        $serviceMap = [];
        foreach ($configs as $repositoryName => $config) {
            $type = $config['type'];

            if (!isset($this->repositoryFactories[$type])) {
                throw new InvalidArgumentException(sprintf(
                    'Unknown repository type "%s", known repository types: "%s"',
                    $type,
                    implode('", "', $repositoryTypes)
                ));
            }

            $factory = $this->repositoryFactories[$type];

            $optionsResolver = new OptionsResolver();
            $factory->configure($optionsResolver);

            try {
                $config = $optionsResolver->resolve($config['options']);
            } catch (\Exception $e) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid configuration for repository "%s"',
                    $repositoryName
                ), null, $e);
            }

            $serviceId = self::getRepositoryServiceId($repositoryName);
            $definition = $factory->create($config);
            $typeMap[$definition->getClass()] = $type;
            $serviceMap[$repositoryName] = $serviceId;

            $container->setDefinition($serviceId, $definition);
            if ($defaultRepositoryName == $repositoryName) {
                $container->setAlias('cmf_resource.repository', $serviceId);
            }
        }

        $registry = $container->getDefinition('cmf_resource.registry');
        $registry->replaceArgument(1, $serviceMap);
        $registry->replaceArgument(2, $typeMap);
    }

    public function addRepositoryFactory($name, RepositoryFactoryInterface $factory)
    {
        $this->repositoryFactories[$name] = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/cmf_resource';
    }
}
