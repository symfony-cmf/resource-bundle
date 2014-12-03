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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler\RepositoryPass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\CmfResourceExtension;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Configuration;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    protected function getContainerExtension()
    {
        return new CmfResourceExtension();
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function provideConfig()
    {
        return array(
            array(__DIR__ . '/fixtures/config.xml'),
            array(__DIR__ . '/fixtures/config.yml'),
        );
    }


    /**
     * @dataProvider provideConfig
     */
    public function testConfig($source)
    {
        $this->assertProcessedConfigurationEquals(array(
            'repository' => array(
                'doctrine_phpcr_odm' => array(
                    array(
                        'name' => 'content',
                        'basepath' => '/cmf/content',
                    ),
                    array(
                        'name' => 'articles',
                        'basepath' => '/cmf/articles',
                    ),
                ),
                'doctrine_phpcr' => array(),
                'composite' => array(
                    array(
                        'name' => 'stuff',
                        'mounts' => array(
                            array(
                                'repository' => 'content',
                                'mountpoint' => '/content',
                            ),
                            array(
                                'repository' => 'articles',
                                'mountpoint' => '/articles',
                            ),
                        ),
                    ),
                ),
            )
        ), array($source));
    }
}


