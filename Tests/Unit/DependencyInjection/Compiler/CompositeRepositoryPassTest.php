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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler\CompositeRepositoryPass;

class CompositeRepositoryPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompositeRepositoryPass());
    }

    public function testCompilerPass()
    {
        $repo = new Definition();
        $repo->addTag('cmf_resource.repository', array(
            'type' => 'doctrine_phpcr_odm',
            'name' => 'repository_alias'
        ));

        $this->setDefinition('cmf_resource.repository.test_repository', $repo);

        $compRepo = new Definition();
        $compRepo->addMethodCall('mount', array('/cmf/foobar', 'some_service_id'));;
        $compRepo->addMethodCall('mount', array('/cmf/barbar', 'repository_alias'));;
        $compRepo->addTag('cmf_resource.repository', array(
            'type' => 'composite',
            'name' => 'foobar'
        ));

        $this->setDefinition('cmf_resource.repository.test_composite', $compRepo);

        $this->compile();

        $methodCalls = $this->container->getDefinition('cmf_resource.repository.test_composite')->getMethodCalls();

        $this->assertCount(2, $methodCalls);
        $mountMethod = reset($methodCalls);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $mountMethod[1][1]);
        $this->assertEquals('some_service_id', (string) $mountMethod[1][1]);

        $mountMethod2 = next($methodCalls);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $mountMethod2[1][1]);
        $this->assertEquals('cmf_resource.repository.test_repository', (string) $mountMethod2[1][1]);
    }
}

