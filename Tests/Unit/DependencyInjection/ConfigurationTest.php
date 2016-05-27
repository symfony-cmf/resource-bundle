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

use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\CmfResourceExtension;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Configuration;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;

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
        return [
            [__DIR__.'/fixtures/config.xml'],
            [__DIR__.'/fixtures/config.yml'],
        ];
    }

    /**
     * @dataProvider provideConfig
     */
    public function testConfig($source)
    {
        $this->assertProcessedConfigurationEquals([
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
                'stuff' => [
                    'type' => 'composite',
                    'options' => [
                        'mounts' => [
                            [
                                'repository' => 'content',
                                'mountpoint' => '/content',
                            ],
                            [
                                'repository' => 'articles',
                                'mountpoint' => '/articles',
                            ],
                        ],
                    ],
                ],
            ],
        ], [$source]);
    }
}
