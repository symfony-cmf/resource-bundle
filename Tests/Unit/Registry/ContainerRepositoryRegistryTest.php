<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Unit\DependencyInjection;

use Symfony\Cmf\Bundle\ResourceBundle\Registry\ContainerRepositoryRegistry;

class ContainerRepositoryRegistryTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $repo;
    private $registry;

    public function setUp()
    {
        $this->container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->repo = $this->prophesize('Puli\Repository\Api\ResourceRepository');
        $serviceMap = [
            'one' => 'repo1_service',
            'two' => 'repo2_service',
        ];

        $this->registry = new ContainerRepositoryRegistry($this->container->reveal(), $serviceMap);
    }

    public function testGet()
    {
        $repo = $this->repo->reveal();
        $this->container->get('repo1_service')->willReturn($repo);

        $this->assertEquals($repo, $this->registry->get('one'));
    }

    public function provideGetDefaultRepository()
    {
        return [
            'normal behaviour' => [],
            'overriden default id' => ['the_default'],
            'no default service' => [null, false],
        ];
    }

    /**
     * @dataProvider provideGetDefaultRepository
     */
    public function testGetDefaultRepository($defaultId = false, $shouldBeCalled = true)
    {
        if ($shouldBeCalled) {
            $this->container
                ->get(false === $defaultId ? 'cmf_resource.repository.default' : $defaultId)
                ->shouldBeCalled();
        }

        if (false !== $defaultId) {
            $registry = new ContainerRepositoryRegistry($this->container->reveal(), [], [], 'the_default');
        } else {
            $registry = new ContainerRepositoryRegistry($this->container->reveal());
        }

        $registry->get();
    }

    public function testGetRepositoryAlias()
    {
        $repo1 = $this->repo->reveal();
        $repo2 = $this->prophesize('Puli\Repository\Api\ResourceRepository')->reveal();

        $this->container->get('repo1_service')->willReturn($repo1);
        $this->container->get('repo2_service')->willReturn($repo2);

        $this->assertEquals('one', $this->registry->getRepositoryAlias($repo1));
    }
}
