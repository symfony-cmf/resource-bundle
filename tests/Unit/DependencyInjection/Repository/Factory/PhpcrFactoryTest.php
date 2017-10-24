<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Unit\DependencyInjection\Repository\Factory;

use PHPCR\NodeInterface;
use PHPCR\SessionInterface;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory\PhpcrFactory;
use Symfony\Cmf\Component\Resource\Repository\PhpcrRepository;

class PhpcrFactoryTest extends FactoryTestCase
{
    private $session;

    public function setUp()
    {
        parent::setUp();
        $this->session = $this->prophesize(SessionInterface::class);
    }

    /**
     * It should add a repository to the container.
     */
    public function testCreate()
    {
        $container = $this->buildContainer(
            $this->resolveOptions([])
        );
        $container->set('doctrine_phpcr.session', $this->session->reveal());

        $this->assertInstanceOf(PhpcrRepository::class, $container->get('repository'));
    }

    /**
     * It should configure the basepath.
     */
    public function testBasepath()
    {
        $container = $this->buildContainer(
            $this->resolveOptions([
                'basepath' => '/cms/foo',
            ])
        );
        $container->set('doctrine_phpcr.session', $this->session->reveal());

        $this->session->getNode('/cms/foo/bar')
            ->willReturn($this->prophesize(NodeInterface::class))
            ->shouldBeCalled();

        $repository = $container->get('repository');
        $this->assertInstanceOf(PhpcrRepository::class, $repository);
        $repository->get('/bar');
    }

    protected function getFactory()
    {
        return new PhpcrFactory();
    }
}
