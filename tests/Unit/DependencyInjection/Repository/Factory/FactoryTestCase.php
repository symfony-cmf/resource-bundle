<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Unit\DependencyInjection\Repository\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class FactoryTestCase extends \PHPUnit_Framework_TestCase
{
    protected $resolver;

    protected function setUp()
    {
        $this->resolver = new OptionsResolver();
    }

    abstract protected function getFactory();

    protected function buildContainer(array $options)
    {
        $container = new ContainerBuilder();

        $definition = $this->getFactory()->create($options);
        $container->setDefinition('repository', $definition);

        return $container;
    }

    protected function resolveOptions(array $options)
    {
        $this->getFactory()->configure($this->resolver);

        return $this->resolver->resolve($options);
    }
}
