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

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class FilesystemRepositoryTest extends BaseTestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Puli Filesystem is not available.');
        $this->repositoryRegistry = $this->getContainer()->get('cmf_resource.registry');
    }

    public function provideGet()
    {
        return [
            ['/foo.txt', 'foo.txt'],
            ['/assets/foo.css', 'foo.css'],
        ];
    }

    /**
     * @dataProvider provideGet
     */
    public function testRepositoryGet($path, $expectedName)
    {
        $repository = $this->repositoryRegistry->get('my_filesystem');
        $res = $repository->get($path);
        $this->assertNotNull($res);
        $this->assertInstanceOf('Puli\Repository\Resource\FileResource', $res);
        $this->assertEquals($expectedName, $res->getName());
    }
}
