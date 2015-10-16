<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
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
        return array(new CmfResourceExtension());
    }

    public function provideExtension()
    {
        return array(
            array(
                array(
                    'repositories' => array(
                        'foobar_odm' => array(
                            'type' => 'doctrine_phpcr_odm',
                            'options' => array(
                                'basepath' => '/cmf/foo',
                            ),
                        ),
                        'foobar_phpcr' => array(
                            'type' => 'doctrine_phpcr',
                            'options' => array(
                                'basepath' => '/cmf/foo',
                            ),
                        ),
                        'foobar_filesystem' => array(
                            'type' => 'filesystem',
                            'options' => array(
                                'base_dir' => '/assets',
                            ),
                        ),
                        'unified' => array(
                            'type' => 'composite',
                            'options' => array(
                                'mounts' => array(
                                    array(
                                        'repository' => 'foobar',
                                        'mountpoint' => '/foobar',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'cmf_resource.repository.foobar_odm',
                    'cmf_resource.repository.foobar_phpcr',
                    'cmf_resource.repository.unified',
                    'cmf_resource.repository.foobar_filesystem',
                ),
            ),
            array(
                array(),
                array(),
            ),
        );
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
