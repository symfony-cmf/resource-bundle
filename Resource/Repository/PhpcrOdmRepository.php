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

class PhpcrOdmRepository implements ResourceRepositoryInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    protected function getManager()
    {
        return $this->managerRegistry->getManager();
    }

    /**
     * TODO: Support "../" or "."
     *
     * {@inheritDoc}
     */
    public function get($path)
    {
        $document = $this->getManager()->find(null, $path);

        if (null === $document) {
            throw new ResourceNotFoundException(sprintf(
                'No PHPCR-ODM document could be found at "%s"',
                $path
            ));
        }

        $absPath = $this->getManager()->getNodeForDocument($document)->getPath();

        $resource = new ObjectResource($absPath, $document);

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
        throw new \Exception('Find not currently supported');
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
