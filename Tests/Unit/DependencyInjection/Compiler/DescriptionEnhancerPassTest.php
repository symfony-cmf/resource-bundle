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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler\DescriptionEnhancerPass;
use Symfony\Component\DependencyInjection\Definition;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Reference;

class DescriptionEnhancerPassTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $factoryDefinition;
    private $pass;

    public function setUp()
    {
        $this->container = $this->prophesize(ContainerBuilder::class);
        $this->factoryDefinition = $this->prophesize(Definition::class);
        $this->pass = new DescriptionEnhancerPass();
    }

    /**
     * It should return early if the factory does not exist.
     */
    public function testReturnEarlyFactoryNotExist()
    {
        $this->container->has('cmf_resource.description.factory')->willReturn(false);
        $this->container->getParameter(Argument::cetera())->shouldNotBeCalled();
        $this->pass->process($this->container->reveal());
    }

    /**
     * It should add description enhancers.
     */
    public function testAddDescriptionEnhancers()
    {
        $this->container->has('cmf_resource.description.factory')->willReturn(true);
        $this->container->getParameter('cmf_resource.description.enabled_enhancers')->willReturn([
            'enhancer_1',
        ]);
        $this->container->findTaggedServiceIds('cmf_resource.description.enhancer')->willReturn([
            'service_1' => [['alias' => 'enhancer_1']],
        ]);

        $this->container->getDefinition('cmf_resource.description.factory')->willReturn($this->factoryDefinition->reveal());
        $this->factoryDefinition->replaceArgument(0, [new Reference('service_1')])->shouldBeCalled();

        $this->pass->process($this->container->reveal());
    }

    /**
     * It should throw an exception if the tag does not have the "alias" key.
     *
     * @expectedException Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @expectedExceptionMessage has no "alias" attribute
     */
    public function testHasNoAlias()
    {
        $this->container->has('cmf_resource.description.factory')->willReturn(true);
        $this->container->getParameter('cmf_resource.description.enabled_enhancers')->willReturn([
            'enhancer_1',
        ]);
        $this->container->findTaggedServiceIds('cmf_resource.description.enhancer')->willReturn([
            'service_1' => [[]],
        ]);

        $this->pass->process($this->container->reveal());
    }

    /**
     * It should throw an exception if two tags have the same alias.
     *
     * @expectedException Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @expectedExceptionMessage has already been registered
     */
    public function testDuplicatedAlias()
    {
        $this->container->has('cmf_resource.description.factory')->willReturn(true);
        $this->container->getParameter('cmf_resource.description.enabled_enhancers')->willReturn([
            'enhancer_1',
        ]);
        $this->container->findTaggedServiceIds('cmf_resource.description.enhancer')->willReturn([
            'service_1' => [['alias' => 'hello']],
            'service_2' => [['alias' => 'hello']],
        ]);

        $this->pass->process($this->container->reveal());
    }

    /**
     * It should throw an exception if an unknown enhancer is enabled.
     *
     * @expectedException Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unknown description enhancer(s) "three" were enabled, available enhancers: "one", "two"
     */
    public function testUnknownEnhancer()
    {
        $this->container->has('cmf_resource.description.factory')->willReturn(true);
        $this->container->getParameter('cmf_resource.description.enabled_enhancers')->willReturn([
            'three',
        ]);
        $this->container->findTaggedServiceIds('cmf_resource.description.enhancer')->willReturn([
            'service_1' => [['alias' => 'one']],
            'service_2' => [['alias' => 'two']],
        ]);

        $this->pass->process($this->container->reveal());
    }

    /**
     * It should remove inactive enhancers.
     */
    public function testRemoveInactive()
    {
        $this->container->has('cmf_resource.description.factory')->willReturn(true);
        $this->container->getParameter('cmf_resource.description.enabled_enhancers')->willReturn([
            'one',
        ]);
        $this->container->findTaggedServiceIds('cmf_resource.description.enhancer')->willReturn([
            'service_1' => [['alias' => 'one']],
            'service_2' => [['alias' => 'two']],
        ]);
        $this->container->getDefinition('cmf_resource.description.factory')->willReturn($this->factoryDefinition->reveal());

        $this->container->removeDefinition('service_2')->shouldBeCalled();
        $this->factoryDefinition->replaceArgument(0, [new Reference('service_1')])->shouldBeCalled();

        $this->pass->process($this->container->reveal());
    }
}
