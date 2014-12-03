<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Unit\DependencyInjection\Compiler;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler\FactoryPass;
use Symfony\Component\DependencyInjection\Definition;

class FactoryPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FactoryPass());
    }

    public function testCompilerPass()
    {
        $factoryDefinition = new Definition();
        $factoryDefinition->setArguments(array(
            new Definition(),
            array()
        ));
        $this->setDefinition('cmf_resource.repository.factory.container', $factoryDefinition);

        $repositoryDefinition = new Definition();
        $repositoryDefinition->addTag('cmf_resource.repository', array('name' => 'test_repository'));
        $this->setDefinition('cmf_resource.repository.test', $repositoryDefinition);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'cmf_resource.repository.factory.container',
            1,
            array(
                'test_repository' => 'cmf_resource.repository.test'
            )
        );
    }
}

