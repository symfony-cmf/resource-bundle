<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Functional;

use PHPCR\NodeInterface;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
abstract class PhpcrRepositoryTestCase extends RepositoryTestCase
{
    /**
     * @dataProvider provideGet
     */
    public function testRepositoryGet($path, $expectedName)
    {
        $res = $this->getRepository()->get($path);
        $this->assertNotNull($res);
        $payload = $res->getPayload();

        $this->assertEquals(
            $expectedName,
            ($payload instanceof NodeInterface ? $payload->getName() : $payload->getNodeName())
        );
    }

    /**
     * @dataProvider provideFind
     */
    public function testRepositoryFind($pattern, $nbResults)
    {
        $res = $this->getRepository()->find($pattern);
        $this->assertCount($nbResults, $res);
    }

    /**
     * @dataProvider provideMove
     */
    public function testRepositoryMove($sourcePath, $targetPath, $expectedNodeName)
    {
        $this->getRepository()->move($sourcePath, $targetPath);

        $this->assertEquals($expectedNodeName, $this->session->getNode('/test'.$targetPath)->getName());
    }

    /**
     * @dataProvider provideDelete
     * @expectedException \PHPCR\PathNotFoundException
     */
    public function testRepositoryRemove($path, $expectedDeleted)
    {
        $this->getRepository()->remove($path);

        $this->session->getNode($path);
    }

    public function provideGet()
    {
        return [
            ['/foo', 'foo'],
            ['/bar', 'bar'],
            ['/', 'test'],
        ];
    }

    public function provideFind()
    {
        return [
            ['/*', 2],
            ['/', 1],
        ];
    }

    public function provideMove()
    {
        return [
            ['/foo', '/foo-bar', 'foo-bar'],
            ['/foo', '/bar/foo', 'foo'],
        ];
    }

    public function provideDelete()
    {
        return [
            ['/foo', 2],
            ['/bar', 1],
        ];
    }

    public function provideAdd()
    {
        return [
            ['/', 'blubb'],
            ['/foo', 'blubb'],
        ];
    }
}
