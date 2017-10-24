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

use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory\DoctrinePhpcrOdmFactory;
use Symfony\Cmf\Component\Resource\Repository\PhpcrOdmRepository;

class DoctrinePhpcrOdmFactoryTest extends FactoryTestCase
{
    private $session;
    private $manager;

    public function setUp()
    {
        parent::setUp();
        $this->registry = $this->prophesize(ManagerRegistry::class);
        $this->manager = $this->prophesize(DocumentManagerInterface::class);
        $this->registry->getManager()->willReturn($this->manager->reveal());
    }

    /**
     * It should add a repository to the container.
     */
    public function testCreate()
    {
        $container = $this->buildContainer(
            $this->resolveOptions([])
        );
        $container->set('doctrine_phpcr', $this->registry->reveal());

        $this->assertInstanceOf(PhpcrOdmRepository::class, $container->get('repository'));
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
        $container->set('doctrine_phpcr', $this->registry->reveal());

        $this->manager->find(null, '/cms/foo/bar')
            ->willReturn(new \stdClass())
            ->shouldBeCalled();

        $repository = $container->get('repository');
        $this->assertInstanceOf(PhpcrOdmRepository::class, $repository);
        $repository->get('/bar');
    }

    protected function getFactory()
    {
        return new DoctrinePhpcrOdmFactory();
    }
}
