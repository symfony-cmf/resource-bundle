<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Unit\DependencyInjection;

use Prophecy\Argument;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\CmfResourceExtension;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory\RepositoryFactoryInterface;
use Symfony\Cmf\Component\Resource\Puli\Api\ResourceRepository;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class CmfResourceExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $extension;
    private $repositoryFactory;

    public function setUp()
    {
        $this->repositoryFactory = $this->prophesize(RepositoryFactoryInterface::class);
        $this->container = new ContainerBuilder();
        $this->extension = new CmfResourceExtension();

        $repository = $this->prophesize(ResourceRepository::class);
        $this->extension->addRepositoryFactory('foobar', $this->repositoryFactory->reveal());
        $this->repositoryFactory->create([])->willReturn(new Definition(get_class($repository->reveal())));
        $this->repositoryFactory->configure(Argument::type(OptionsResolver::class))->willReturn(null);
    }

    /**
     * It should set the repositories into the registry.
     * It should load the type map into the registry.
     */
    public function testLoadRegistry()
    {
        $this->extension->load([
            [
                'repositories' => [
                    'test' => [
                        'type' => 'foobar',
                    ],
                ],
            ],
        ], $this->container);

        $registry = $this->container->get('cmf_resource.registry');
        $repository = $registry->get('test');

        $this->assertInstanceOf(ResourceRepository::class, $repository);
        $this->assertEquals('foobar', $registry->getRepositoryType($repository));
    }

    /**
     * Repositories: It should throw an exception if an unknown type is specified.
     *
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unknown repository type "bar", known repository types: "foobar"
     */
    public function testConfigRepositoryNoType()
    {
        $this->extension->load([
            [
                'repositories' => [
                    'test' => [
                        'type' => 'bar',
                    ],
                ],
            ],
        ], $this->container);
    }

    /**
     * Repositories: It should wrap exceptions thrown by the options resolver.
     */
    public function testWrapExceptionOptionsResolver()
    {
        $this->repositoryFactory->configure(Argument::type(OptionsResolver::class))->will(function ($args) {
            $args[0]->setRequired('barbar');
        });

        try {
            $this->extension->load([
                [
                    'repositories' => [
                        'test' => [
                            'type' => 'foobar',
                        ],
                    ],
                ],
            ], $this->container);
            $this->fail('No exception has been thrown');
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertContains('Invalid configuration for repository "test"', $e->getMessage());
            $this->assertInstanceOf(MissingOptionsException::class, $e->getPrevious());
        }
    }
}
