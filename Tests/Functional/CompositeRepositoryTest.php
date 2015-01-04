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

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Doctrine\ODM\PHPCR\Document\Generic;

class CompositeRepositoryTest extends RepositoryTestCase
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
        $repository = $this->repositoryFactory->create('stuff');
        $res = $repository->get($path);
        $this->assertNotNull($res);
        $document = $res->getDocument();

        $this->assertEquals($expectedName, $document->getNodeName());
    }

    public function provideFind()
    {
        return array(
            array('/content/*', 2)
        );
    }

    /**
     * @dataProvider provideFind
     */
    public function testRepositoryFind($pattern, $nbResults)
    {
        $repository = $this->repositoryFactory->create('stuff');
        $res = $repository->find($pattern);
        $this->assertCount($nbResults, $res);
    }
}
