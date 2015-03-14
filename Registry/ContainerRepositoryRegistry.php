<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Registry;

use Symfony\Cmf\Component\Resource\RepositoryFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Puli\Repository\Api\Resource\Resource;
use Puli\Repository\Api\ResourceRepository;

/**
 * Registry which acts as a proxy to the Symfony DI container
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ContainerRepositoryRegistry implements RepositoryRegistryInterface
{
    /**
     * @var array
     */
    private $serviceMap = array();

    /**
     * @var array
     */
    private $typeMap;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $repositoryTypes = array();

    /**
     * @param ContainerInterface $container
     * @param array $serviceMap
     * @param array $typeMap
     */
    public function __construct(ContainerInterface $container, $serviceMap = array(), $typeMap = array())
    {
        $this->serviceMap = $serviceMap;
        $this->container = $container;
        $this->typeMap = $typeMap;
    }

    /**
     * {@inheritDoc}
     */
    public function get($repositoryAlias)
    {
        return $this->container->get($this->getRepositoryServiceId($repositoryAlias));
    }

    /**
     * {@inheritDoc}
     */
    public function getRepositoryAlias(ResourceRepository $resourceRepository)
    {
        foreach ($this->serviceMap as $alias => $serviceId) {
            $repository = $this->container->get($serviceId);

            if ($repository === $resourceRepository) {
                return $alias;
            }
        }

        throw new \RuntimeException(sprintf(
            'Could not determine registration name for repository of type "%s".' .
            'No matching repository exists in the registry',
            get_class($resourceRepository)
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getRepositoryType(ResourceRepository $resourceRepository)
    {
        $class = get_class($resourceRepository);
        $typeMap = array_flip($this->typeMap);

        if (!isset($typeMap[$class])) {
            throw new \RuntimeException(sprintf(
                'Type for repository class "%s" is not registered, known classes: "%s"',
                $class,
                implode('", "', $this->typeMap)
            ));
        }

        return $typeMap[$class];
    }

    /**
     * Return the service ID for the given repository alias
     *
     * @param string $alias
     * @return string
     */
    private function getRepositoryServiceId($alias)
    {
        if (!isset($this->serviceMap[$alias])) {
            throw new \InvalidArgumentException(sprintf(
                'Repository with alias "%s" has not been registered, registered aliases: "%s"',
                $alias,
                implode('", "', array_keys($this->serviceMap))
            ));
        }

        return $this->serviceMap[$alias];
    }
}
