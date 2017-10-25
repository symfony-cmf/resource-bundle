<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Unit\Registry;

use Symfony\Cmf\Bundle\ResourceBundle\Registry\RepositoryRegistry;
use Symfony\Cmf\Component\Resource\Puli\Api\ResourceRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RepositoryRegistryTest extends \PHPUnit_Framework_TestCase
{
    private $repository;
    private $container;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->repository = $this->prophesize(ResourceRepository::class);
    }

    /**
     * It should return a configured repository.
     * It should be able to recall the name of the returned repository.
     * It should be able to recall the type of the returned repository.
     */
    public function testReturnConfigured()
    {
        $registry = $this->createRegistry(
            [
                'test' => 'test_repository',
            ]
        );

        $repository = $registry->get('test');
        $this->assertInstanceOf(ResourceRepository::class, $repository);
        $this->assertEquals('test', $registry->getRepositoryName($repository));
        $this->assertEquals('test/type', $registry->getRepositoryType($repository));
    }

    /**
     * It should throw an exception if an unknown repository is requested.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Repository "barbar" has not been registered, available repositories: "test"
     */
    public function testUnknownRepositoryName()
    {
        $registry = $this->createRegistry(
            [
                'test' => 'test_repository',
            ]
        );

        $registry->get('barbar');
    }

    /**
     * It should throw an exception if an name is requested for an unknown repository.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage I don't know what its name is
     */
    public function testGetNameUnknownRepository()
    {
        $registry = $this->createRegistry([]);

        $repository = $this->prophesize(ResourceRepository::class);
        $registry->getRepositoryName($repository->reveal());
    }

    /**
     * It should throw an exception if a type is requsted for an unknown repository.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage could not determine its type
     */
    public function testGetTypeUnknownRepository()
    {
        $registry = $this->createRegistry([]);

        $repository = $this->prophesize(ResourceRepository::class);
        $registry->getRepositoryType($repository->reveal());
    }

    /**
     * It should return the names of registered repositories.
     */
    public function testNames()
    {
        $registry = $this->createRegistry(
            [
                'test' => 'test_repository',
                'tset' => 'tset_repository',
            ]
        );

        $this->assertEquals(['test', 'tset'], $registry->names());
    }

    /**
     * It should return all registered repositories.
     */
    public function testAll()
    {
        $registry = $this->createRegistry(
            [
                'test' => 'test_repository',
                'tset' => 'tset_repository',
            ]
        );

        $all = $registry->all();
        $this->assertCount(2, $all);
        $this->assertInstanceOf(ResourceRepository::class, $all['test']);
        $this->assertInstanceOf(ResourceRepository::class, $all['tset']);
    }

    private function createRegistry(array $serviceMap)
    {
        $typeMap = [];
        foreach ($serviceMap as $repositoryName => $serviceId) {
            $repository = $this->prophesize(ResourceRepository::class);
            $this->container->register(
                $serviceId,
                get_class($repository->reveal())
            );
            $typeMap[get_class($repository->reveal())] = 'test/type';
        }

        return new RepositoryRegistry(
            $this->container,
            $serviceMap,
            $typeMap,
            'default'
        );
    }
}
