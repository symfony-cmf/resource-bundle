<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Functional;

class CompositeRepositoryTest extends PhpcrRepositoryTestCase
{
    public function provideGet()
    {
        return array(
            array('/content/foo', 'foo'),
            array('/content/bar', 'bar'),
        );
    }

    /**
     * @dataProvider provideGet
     */
    public function testRepositoryGet($path, $expectedName)
    {
        $repository = $this->repositoryRegistry->get('stuff');
        $res = $repository->get($path);
        $this->assertNotNull($res);
        $document = $res->getPayload();

        $this->assertEquals($expectedName, $document->getNodeName());
    }

    public function provideFind()
    {
        return array(
            array('/content/*', 2),
        );
    }

    /**
     * @dataProvider provideFind
     */
    public function testRepositoryFind($pattern, $nbResults)
    {
        $repository = $this->repositoryRegistry->get('stuff');
        $res = $repository->find($pattern);
        $this->assertCount($nbResults, $res);
    }
}
