<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
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
    private $session;

    /**
     * @var NodeInterface
     */
    private $baseNode;

    protected function setUp()
    {
        parent::setUp();

        $this->session = $this->getContainer()->get('doctrine_phpcr.session');
        $this->db('PHPCR')->purgeRepository(true);

        $rootNode = $this->session->getRootNode();

        // the resource repository is based at "/test"
        $this->baseNode = $rootNode->addNode('/test');
    }

    /**
     * @dataProvider provideGet
     */
    public function testGet($path, $expectedName)
    {
        $sub1 = $this->baseNode->addNode('node-1');
        $sub1->addNode('node-1-1');
        $sub1->addNode('node-1-2');
        $this->baseNode->addNode('node-2');
        $this->session->save();

        $this->session->save();
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
        $sub1 = $this->baseNode->addNode('node-1');
        $sub1->addNode('node-1-1');
        $sub1->addNode('node-1-2');
        $this->baseNode->addNode('node-2');
        $this->session->save();

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
        $sub1 = $this->baseNode->addNode('node-1');
        $sub1->addNode('node-1-1');
        $sub1->addNode('node-1-2');
        $this->baseNode->addNode('node-2');
        $this->session->save();

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
        $sub1 = $this->baseNode->addNode('node-1');
        $sub1->addNode('node-1-1');
        $sub1->addNode('node-1-2');
        $this->baseNode->addNode('node-2');
        $this->session->save();

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

    /**
     * @dataProvider provideReorder
     */
    public function testReorder($path, $index, $newOrder)
    {
        $this->baseNode->addNode('node-1');
        $this->baseNode->addNode('node-2');
        $this->baseNode->addNode('node-3');
        $this->session->save();

        $this->getRepository()->reorder($path, $index);

        $node = $this->session->getNode('/test'.$path);
        $parent = $node->getParent();
        $nodeNames = $parent->getNodeNames();
        $this->assertEquals($newOrder, iterator_to_array($nodeNames));
    }

    public function provideReorder()
    {
        return [
            ['/node-1', 1, ['node-2', 'node-1', 'node-3']],
            ['/node-1', 2, ['node-2', 'node-3', 'node-1']],
            ['/node-1', 0, ['node-1', 'node-2', 'node-3']],
            ['/node-3', 0, ['node-3', 'node-1', 'node-2']],
            ['/node-1', 66, ['node-2', 'node-3', 'node-1']],
        ];
    }

    /**
     * It should throw an exception if the reorder index is less than zero.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Reorder position cannot be negative, got: -5
     */
    public function testReorderNegative()
    {
        $this->getRepository()->reorder('/foo', -5);
    }
}
