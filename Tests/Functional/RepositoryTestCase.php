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

use Doctrine\ODM\PHPCR\DocumentManager;
use PHPCR\SessionInterface;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
abstract class RepositoryTestCase extends BaseTestCase
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var  RepositoryRegistryInterface
     */
    protected $repositoryRegistry;

    /**
     * @var SessionInterface
     */
    protected $session;

    public function setUp()
    {
        $this->dm = $this->db('PHPCR')->getOm();
        $this->repositoryRegistry = $this->container->get('cmf_resource.registry');
        $this->session = $this->getContainer()->get('doctrine_phpcr.session');

        $this->db('PHPCR')->purgeRepository(true);

        $this->session->getRootNode()->addNode('/test');
        $rootNode = $this->session->getNode('/test');
        $rootNode->addNode('/test/foo');
        $rootNode->addNode('/test/bar');
        $this->session->getNode('/test/foo')->addNode('/test/foo/child');

        $this->session->save();
    }

    abstract protected function getRepository();
}
