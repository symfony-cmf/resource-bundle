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
    private $repositoryServiceMap = array();

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param array repositoryServiceMap
     */
    public function __construct(ContainerInterface $container, $repositoryServiceMap = array())
    {
        $this->repositoryServiceMap = $repositoryServiceMap;
        $this->container = $container;
    }

    /**
     * Return a new instance of the named repository
     *
     * @param string $name
     *
     * @return Puli\Resource\RepositoryInterface
     */
    public function get($repositoryName)
    {
        return $this->container->get($this->getRepositoryServiceId($repositoryName));
    }

    private function getRepositoryServiceId($name)
    {
        if (!isset($this->repositoryServiceMap[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Repository with name "%s" has not been registered, registered names: "%s"',
                $name,
                implode('", "', array_keys($this->repositoryServiceMap))
            ));
        }

        return $this->repositoryServiceMap[$name];
    }

    public function getName(ResourceRepository $resourceRepository)
    {
        foreach ($this->repositoryServiceMap as $name => $serviceId) {
            $repository = $this->container->get($serviceId);

            if ($repository === $resourceRepository) {
                return $name;
            }
        }

        throw new \RuntimeException(sprintf(
            'Could not determine registration name for repository of type "%s".' .
            'No matching repository exists in the registry',
            get_class($resourceRepository)
        ));
    }
}
