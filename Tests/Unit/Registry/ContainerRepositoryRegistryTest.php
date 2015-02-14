<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Unit\DependencyInjection;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Bundle\ResourceBundle\Registry\ContainerRepositoryRegistry;
use Prophecy\Argument;

class ContainerRepositoryRegistryTest extends ProphecyTestCase
{
    public function setUp()
    {
        $this->container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->repo1 = $this->prophesize('Puli\Repository\Api\ResourceRepository');
        $this->repo2 = $this->prophesize('Puli\Repository\Api\ResourceRepository');
        $serviceMap = array(
            'one' => 'repo1_service',
            'two' => 'repo2_service',
        );

        $containerMap = array(
            'repo1_service' => $this->repo1->reveal(),
            'repo2_service' => $this->repo2->reveal(),
        );
        $this->container->get(Argument::any())->will(function ($args) use ($containerMap) {
            $name = array_shift($args);
            return $containerMap[$name];
        });

        $this->registry = new ContainerRepositoryRegistry($this->container->reveal(), $serviceMap);
    }

    public function testGet()
    {
        $res = $this->registry->get('one');
        $this->assertSame($res, $this->repo1->reveal());
    }

    public function testGetRepositoryAlias()
    {
        $res = $this->registry->getRepositoryAlias($this->repo1->reveal());
        $this->assertEquals('one', $res);
    }
}

