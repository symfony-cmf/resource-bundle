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
use PHPCR\PathNotFoundException;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
abstract class PhpcrRepositoryTestCase extends RepositoryTestCase
{
    /**
     * @var SessionInterface
     */
    protected $session;

    protected function setUp()
    {
        parent::setUp();

        $this->session = $this->getContainer()->get('doctrine_phpcr.session');
        $this->db('PHPCR')->purgeRepository(true);

        $rootNode = $this->session->getRootNode();

        // the resource repository is based at "/test"
        $node = $rootNode->addNode('/test');

        $sub1 = $node->addNode('node-1');
        $sub1->addNode('node-1-1');
        $sub1->addNode('node-1-2');
        $node->addNode('node-2');

        $this->session->save();
    }

    /**
     * @dataProvider provideGet
     */
    public function testGet($path, $expectedName)
    {
        $res = $this->getRepository()->get($path);
        $this->assertNotNull($res);
        $payload = $res->getPayload();

        $this->assertEquals(
            $expectedName,
            ($payload instanceof NodeInterface ? $payload->getName() : $payload->getNodeName())
        );
    }

    public function provideGet()
    {
        return [
            ['/node-1', 'node-1'],
            ['/node-2', 'node-2'],
            ['/', 'test'],
        ];
    }

    /**
     * @dataProvider provideFind
     */
    public function testFind($pattern, $nbResults)
    {
        $res = $this->getRepository()->find($pattern);
        $this->assertCount($nbResults, $res);
    }

    public function provideFind()
    {
        return [
            ['/*', 2],
            ['/', 1],
        ];
    }

    /**
     * @dataProvider provideMove
     */
    public function testMove($sourcePath, $targetPath, $expectedPaths)
    {
        $expectedNbMoved = count($expectedPaths);

        $nbMoved = $this->getRepository()->move($sourcePath, $targetPath);
        $this->assertEquals($expectedNbMoved, $nbMoved);

        foreach ($expectedPaths as $path) {
            try {
                $this->session->getNode($path);
            } catch (\Exception $e) {
                $this->fail(sprintf(
                    'Could not find node at expected path: "%s": %s',
                    $path, $e->getMessage()
                ));
            }
        }
    }

    public function provideMove()
    {
        return [
            ['/node-1', '/foo-bar', ['/test/foo-bar']],
            ['/node-2', '/node-1/foo', ['/test/node-1/foo']],
            ['/node-1/*', '/node-2', ['/test/node-2/node-1-1', '/test/node-2/node-1-2']],
        ];
    }

    /**
     * @dataProvider provideRemove
     */
    public function testRemove($path, $expectedRemovedPaths)
    {
        $expectedNbRemoved = count($expectedRemovedPaths);
        $nbRemoved = $this->getRepository()->remove($path);
        $this->assertEquals($expectedNbRemoved, $nbRemoved);

        foreach ($expectedRemovedPaths as $path) {
            try {
                $this->session->getNode($path);
                $this->fail('Node at "%s" still exists');
            } catch (PathNotFoundException $e) {
                // ok then.
            }
        }
    }

    public function provideRemove()
    {
        return [
            ['/node-1', ['/test/node-1']],
            ['/node-2', ['/test/node-2']],
            ['/*', ['/test/node-1', '/test/node-2']],
            ['/node-1/*', ['/test/node-1-1', '/test/node-1-2']],
        ];
    }
}
