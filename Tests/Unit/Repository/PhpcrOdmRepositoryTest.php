<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Bundle\ResourceBundle\Resource\Repository\PhpcrOdmRepository;

class PhpcrOdmRepositoryTest extends ProphecyTestCase
{
    public function setUp()
    {
        $this->documentManager = $this->prophesize('Doctrine\ODM\PHPCR\DocumentManager');
        $this->managerRegistry = $this->prophesize('Doctrine\Common\Persistence\ManagerRegistry');
        $this->node = $this->prophesize('PHPCR\NodeInterface');

        $this->managerRegistry->getManager()->willReturn($this->documentManager);
        $this->repository = new PhpcrOdmRepository($this->managerRegistry->reveal());
        $this->object = new \stdClass;
    }

    public function testGet()
    {
        $this->documentManager->find(null, '/cmf/foobar')->willReturn($this->object);
        $this->documentManager->getNodeForDocument($this->object)->willReturn($this->node);
        $this->node->getPath()->willReturn('/cmf/foobar');
        $res = $this->repository->get('/cmf/foobar');

        $this->assertInstanceOf('Symfony\Cmf\Bundle\ResourceBundle\Resource\ObjectResource', $res);
        $this->assertEquals('/cmf', $res->getPath());
        $this->assertEquals('foobar', $res->getName());
        $this->assertSame($this->object, $res->getObject());
    }
}
