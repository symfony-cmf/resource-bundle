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

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Doctrine\ODM\PHPCR\Document\Generic;

class PhpcrRepositoryTestCase extends BaseTestCase
{
    protected $dm;

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
}
