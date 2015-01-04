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
                    'repository' => array(
                        'doctrine_phpcr_odm' => array(
                            'foobar' => array(
                                'basepath' => '/cmf/foo',
                            ),
                        ),
                        'doctrine_phpcr' => array(
                            'foobar' => array(
                                'basepath' => '/cmf/foo',
                            ),
                        ),
                        'composite' => array(
                            'unified' => array(
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
                    'cmf_resource.repository.doctrine_phpcr_odm.foobar',
                    'cmf_resource.repository.doctrine_phpcr.foobar',
                    'cmf_resource.repository.composite.unified',
                ),
            ),
            array(
                array(
                ),
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
