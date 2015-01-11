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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler\RegistryPass;
use Symfony\Component\DependencyInjection\Definition;

class RegistryPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegistryPass());
    }

    public function testCompilerPass()
    {
        $registryDefinition = new Definition();
        $registryDefinition->setArguments(array(
            new Definition(),
            array(),
        ));
        $this->setDefinition('cmf_resource.registry.container', $registryDefinition);

        $repositoryDefinition = new Definition();
        $repositoryDefinition->addTag('cmf_resource.repository', array('name' => 'test_repository'));
        $this->setDefinition('cmf_resource.repository.test', $repositoryDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'cmf_resource.registry.container',
            1,
            array(
                'test_repository' => 'cmf_resource.repository.test',
            )
        );
    }
}
