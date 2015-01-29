<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Functional;

use Doctrine\ODM\PHPCR\Document\Generic;

class PhpcrOdmRepositoryTest extends PhpcrRepositoryTestCase
{
    public function setUp()
    {
        $this->dm = $this->db('PHPCR')->getOm();

        $this->db('PHPCR')->purgeRepository(true);
        $this->db('PHPCR')->createTestNode();

        $rootDocument =  $this->dm->find(null, '/test');
        $document = new Generic();
        $document->setNodeName('foo');
        $document->setParentDocument($rootDocument);
        $this->dm->persist($document);

        $document = new Generic();
        $document->setNodeName('bar');
        $document->setParentDocument($rootDocument);
        $this->dm->persist($document);
        $this->dm->flush();

        $this->repositoryRegistry = $this->container->get('cmf_resource.registry');
    }

    public function provideGet()
    {
        return array(
            array('/foo', 'foo'),
            array('/bar', 'bar'),
            array('/', 'test'),
        );
    }

    /**
     * @dataProvider provideGet
     */
    public function testRepositoryGet($path, $expectedName)
    {
        $repository = $this->repositoryRegistry->get('test_repository');
        $res = $repository->get($path);
        $this->assertNotNull($res);
        $document = $res->getDocument();

        $this->assertEquals($expectedName, $document->getNodeName());
    }

    public function provideFind()
    {
        return array(
            array('/*', 2),
            array('/', 1),
        );
    }

    /**
     * @dataProvider provideFind
     */
    public function testRepositoryFind($pattern, $nbResults)
    {
        $repository = $this->repositoryRegistry->get('test_repository');
        $res = $repository->find($pattern);
        $this->assertCount($nbResults, $res);
    }
}
