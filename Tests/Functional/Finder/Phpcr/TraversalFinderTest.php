<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Resource\Finder\Phpcr;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Bundle\ResourceBundle\Resource\Repository\PhpcrOdmRepository;
use Jackalope\RepositoryFactoryFilesystem;
use PHPCR\SimpleCredentials;
use Symfony\Cmf\Bundle\ResourceBundle\Resource\Finder\SelectorParser;
use Symfony\Cmf\Bundle\ResourceBundle\Resource\Finder\Phpcr\TraversalFinder;
use Symfony\Component\Filesystem\Filesystem;

class TraversalFinderTest extends \PHPUnit_Framework_TestCase
{
    private $session;

    public function setUp()
    {
        $path = __DIR__ . '/../../../Resources/data';

        $sfFs = new Filesystem();
        $sfFs->remove($path);
        $factory = new RepositoryFactoryFilesystem();
        $repository = $factory->getRepository(array(
            'path' => $path,
            'search.enabled' => false,
        ));
        $credentials = new SimpleCredentials('admin', 'admin');
        $this->session = $repository->login($credentials);

        $parser = new SelectorParser();
        $this->finder = new TraversalFinder($this->session, $parser);
    }

    public function provideFind()
    {
        return array(
            array(
                '/foo/*/*',
                array(
                    '/foo/foo/baz',
                ),
            ),
            array(
                '/*/foo/*',
                array(
                    '/foo/foo/baz',
                    '/bar/foo/baz',
                ),
            ),
            array(
                '/*/bar',
                array(
                    '/foo/bar',
                    '/bar/bar',
                ),
            ),
            array(
                '/foo/*',
                array(
                    '/foo/foo',
                    '/foo/bar',
                ),
            ),
            array(
                '/',
                array(
                    '/',
                ),
            ),
            array(
                '/foo',
                array(
                    '/foo',
                ),
            ),
            array(
                '/foo/bar',
                array(
                    '/foo/bar'
                ),
            ),
            array(
                '/*/*',
                array(
                    '/foo/foo',
                    '/foo/bar',
                    '/bar/foo',
                    '/bar/bar',
                ),
            ),
        );
    }

    /**
     * @dataProvider provideFind
     */
    public function testFind($path, $expected)
    {
        $this->loadFixtures();
        $nodes = $this->finder->find($path);

        $paths = array();
        foreach ($nodes as $node) {
            $paths[] = $node->getPath();
        }

        $this->assertSame($expected, $paths);
    }

    private function loadFixtures()
    {
        $rootNode = $this->session->getRootNode();
        $node1 = $rootNode->addNode('foo');
            $node1->addNode('bar');
                $node4 = $node1->addNode('foo');
                    $node4->addNode('baz');
        $node2 = $rootNode->addNode('bar');
        $node2->addNode('bar');
        $node3 = $node2->addNode('foo');
        $node3->addNode('baz');
        $this->session->save();
    }
}

