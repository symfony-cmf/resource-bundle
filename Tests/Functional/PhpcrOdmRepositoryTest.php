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

use Doctrine\ODM\PHPCR\Document\Generic;
use PHPCR\Util\PathHelper;
use Puli\Repository\Resource\Collection\ArrayResourceCollection;
use Symfony\Cmf\Component\Resource\Repository\Resource\PhpcrOdmResource;

class PhpcrOdmPhpcrRepositoryTest extends PhpcrRepositoryTestCase
{
    protected function getRepository()
    {
        return $this->repositoryRegistry->get('test_repository_phpcr_odm');
    }

    /**
     * @dataProvider provideAdd
     */
    public function testRepositoryAddSingleResource($path, $name)
    {
        $document = new Generic();
        $document->setNodeName($name);
        $document->setParentDocument($this->dm->find(null, '/test'.('/' === $path ? '' : $path)));
        $this->dm->persist($document);

        $resource = new PhpcrOdmResource($path, $document);
        $this->getRepository()->add($path, $resource);

        $document = $this->dm->find(null, '/test'.$path.('/' === $path ? '' : '/').$name);
        $this->assertEquals($name, $document->getNodeName());
    }

    /**
     * @dataProvider provideAdd
     */
    public function testRepositoryAddResourceCollection($path, $name)
    {
        $document = new Generic();
        $document->setNodeName($name);
        $document->setParentDocument($this->dm->find(null, '/test'.('/' === $path ? '' : $path)));
        $this->dm->persist($document);

        $resource = new ArrayResourceCollection([new PhpcrOdmResource($path, $document)]);
        $this->getRepository()->add($path, $resource);

        $document = $this->dm->find(null, '/test'.PathHelper::absolutizePath($name, $path));
        $this->assertEquals($name, $document->getNodeName());
    }
}
