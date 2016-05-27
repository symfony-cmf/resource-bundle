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
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class CmfResourceExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [new CmfResourceExtension()];
    }

    public function provideExtension()
    {
        return [
            [
                [
                    'repositories' => [
                        'foobar_odm' => [
                            'type' => 'doctrine_phpcr_odm',
                            'options' => [
                                'basepath' => '/cmf/foo',
                            ],
                        ],
                        'foobar_phpcr' => [
                            'type' => 'doctrine_phpcr',
                            'options' => [
                                'basepath' => '/cmf/foo',
                            ],
                        ],
                        'foobar_filesystem' => [
                            'type' => 'filesystem',
                            'options' => [
                                'base_dir' => '/assets',
                            ],
                        ],
                        'unified' => [
                            'type' => 'composite',
                            'options' => [
                                'mounts' => [
                                    [
                                        'repository' => 'foobar',
                                        'mountpoint' => '/foobar',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'cmf_resource.repository.foobar_odm',
                    'cmf_resource.repository.foobar_phpcr',
                    'cmf_resource.repository.unified',
                    'cmf_resource.repository.foobar_filesystem',
                ],
            ],
            [
                [],
                [],
            ],
        ];
    }

    /**
     * @dataProvider provideExtension
     */
    public function testExtension($config, $expectedServiceIds)
    {
        $this->load($config);

        foreach ($expectedServiceIds as $expectedServiceId) {
            $this->assertContainerBuilderHasService($expectedServiceId);
        }
    }
}
