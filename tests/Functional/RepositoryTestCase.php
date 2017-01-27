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

use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
abstract class RepositoryTestCase extends BaseTestCase
{
    /**
     * @var RepositoryRegistryInterface
     */
    protected $repositoryRegistry;

    protected function setUp()
    {
        $this->repositoryRegistry = $this->getContainer()->get('cmf_resource.registry');
    }

    abstract protected function getRepository();
}
