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

class CompositeRepositoryTest extends RepositoryTestCase
{
    const REPOSITORY = 'stuff';

    public function provideGet()
    {
        return [
            ['/content/foo', 'foo'],
            ['/content/bar', 'bar'],
        ];
    }

    /**
     * @dataProvider provideGet
     */
    public function testRepositoryGet($path, $expectedName)
    {
        $res = $this->getRepository()->get($path);
        $this->assertNotNull($res);
        $document = $res->getPayload();

        $this->assertEquals($expectedName, $document->getNodeName());
    }

    public function provideFind()
    {
        return [
            ['/content/*', 2],
        ];
    }

    /**
     * @dataProvider provideFind
     */
    public function testRepositoryFind($pattern, $nbResults)
    {
        $res = $this->getRepository()->find($pattern);
        $this->assertCount($nbResults, $res);
    }

    protected function getRepository()
    {
        return $this->repositoryRegistry->get('stuff');
    }
}
