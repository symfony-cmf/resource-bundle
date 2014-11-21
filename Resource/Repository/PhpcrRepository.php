<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Resource\Repository;

use Puli\Repository\ResourceRepositoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Puli\Repository\ResourceNotFoundException;
use Puli\Resource\Collection\ResourceCollection;
use Symfony\Cmf\Bundle\ResourceBundle\Resource\ObjectResource;

class PhpcrRepository implements ResourceRepositoryInterface
{
    /**
     * @var ManagerRegistry
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * TODO: Support "../" or "."
     *
     * {@inheritDoc}
     */
    public function get($path)
    {
        try {
            $node = $this->session->getNode();
        } catch (\PathNotFoundException $e) {
            throw new ResourceNotFoundException(sprintf(
                'No PHPCR node could be found at "%s"',
                $path
            ), null, $e);
        }

        $resource = new ObjectResource($node->getPath(), $node);

        return $resource;
    }

    /**
     * We could support this by implenting some glob utility which could
     * also be used in PHPCR-Shell or by using XPath queries.
     *
     * {@inheritDoc}
     */
    public function find($selector)
    {
        $nodes = $this->globFinder->find($selector);

        return $nodes;
    }

    /**
     * {@inheritDoc}
     */
    public function contains($selector)
    {
        throw new \Exception('Contains not currently supported');
    }

    /**
     * {@inheritDoc}
     */
    public function getByTag($tag)
    {
        throw new \Exception('Get by tag not currently supported');
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        return array();
    }
}
