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

use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\CmfResourceExtension;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    public function setUp()
    {
        $extension = new CmfResourceExtension();
        $this->container = new ContainerBuilder();
        $this->container->registerExtension($extension);
    }

    public function testXmlConfig()
    {
        $loader = new XmlFileLoader($this->container, new FileLocator());
        $loader->load(__DIR__.'/fixtures/config.xml');
        $this->assertConfig(
            $this->container->getExtensionConfig('cmf_resource')
        );
    }

    public function testYmlConfig()
    {
        $loader = new YamlFileLoader($this->container, new FileLocator());
        $loader->load(__DIR__.'/fixtures/config.yml');
        $this->assertConfig(
            $this->container->getExtensionConfig('cmf_resource')
        );
    }

    public function assertConfig($source)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $source);
        $this->assertEquals([
            'description' => [
                'enhancers' => [],
            ],
            'repositories' => [
                'content' => [
                    'type' => 'doctrine_phpcr_odm',
                    'options' => [
                        'basepath' => '/cmf/content',
                    ],
                ],
                'articles' => [
                    'type' => 'doctrine_phpcr_odm',
                    'options' => [
                        'basepath' => '/cmf/articles',
                    ],
                ],
            ],
            'default_repository' => 'default',
        ], $config);
    }
}
