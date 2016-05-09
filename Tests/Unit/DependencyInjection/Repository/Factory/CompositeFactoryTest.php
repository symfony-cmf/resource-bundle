<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Unit\DependencyInjection\Repository\Factory;

use Puli\Repository\CompositeRepository;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\CmfResourceExtension;
use Puli\Repository\InMemoryRepository;
use Puli\Repository\Resource\GenericResource;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory\CompositeFactory;

class CompositeFactoryTest extends FactoryTestCase
{
    /**
     * It should add a repository to the container.
     */
    public function testCreate()
    {
        $container = $this->buildContainer(
            $this->resolveOptions([])
        );
        $this->assertInstanceOf(CompositeRepository::class, $container->get('repository'));
    }

    /**
     * It should mount other repositories.
     */
    public function testMount()
    {
        $container = $this->buildContainer(
            $this->resolveOptions([
                'mounts' => [
                    [
                        'mountpoint' => '/path/to',
                        'repository' => 'foo',
                    ],
                    [
                        'mountpoint' => '/path/bar',
                        'repository' => 'bar',
                    ],
                ],
            ])
        );
        $repository1Id = CmfResourceExtension::getRepositoryServiceId('foo');
        $container->register($repository1Id, InMemoryRepository::class)
            ->addMethodCall(
                'add',
                [
                    '/foo',
                    new GenericResource('/foo'),
                ]
            );

        $container->register(
            CmfResourceExtension::getRepositoryServiceId('bar'),
            InMemoryRepository::class
        );

        $repository = $container->get('repository');
        $this->assertInstanceOf(CompositeRepository::class, $container->get('repository'));
        $repository = $repository->get('/path/to/foo')->getRepository();
        $this->assertSame(
            $container->get($repository1Id),
            $repository
        );
    }

    /**
     * It should throw an exception if the mount option is not valid.
     *
     * @dataProvider provideInvalidOptions
     */
    public function testInvalidOptions($options, $expectedMessage)
    {
        $this->setExpectedException(InvalidOptionsException::class, $expectedMessage);
        $this->buildContainer(
            $this->resolveOptions($options)
        );
    }

    public function provideInvalidOptions()
    {
        return [
            [
                [
                    'mounts' => [
                        [
                            'repository' => '/path',
                        ],
                    ],
                ],
                'The "mountpoint" option is required when defining a "composite" repository',
            ],
            [
                [
                    'mounts' => [
                        [
                            'mountpoint' => '/path',
                        ],
                    ],
                ],
                'The "repository" option is required when defining a "composite" repository',
            ],
            [
                [
                    'mounts' => [
                        [
                            'foo' => 'bar',
                            'mountpoint' => 'bar/foo',
                            'repository' => 'barbar',
                            'bar' => 'foo',
                        ],
                    ],
                ],
                'The options "foo", "bar" are not allowed when defining a "composite" repository.',
            ],
        ];
    }

    protected function getFactory()
    {
        return new CompositeFactory();
    }
}
