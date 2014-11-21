<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Resource;

use Puli\Resource\ResourceInterface;

/**
 * Resource for objects, intended for use with content repositories
 * documents but could be used in other contexts (i.e. Doctrine entities).
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ObjectResource implements ResourceInterface
{
    /**
     * @var string
     */
    private $absPath;

    /**
     * @var object
     */
    private $object;

    /**
     * @param string $absPath Absolute path to object (e.g. /cmf/foobar/mynode)
     * @param object $object
     */
    public function __construct($absPath, $object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException(sprintf(
                'The ObjectResource should be passed an object, was passed an "%s"',
                gettype($object)
            ));
        }

        $this->absPath = $absPath;
        $this->object = $object;
    }

    /**
     * Return the object
     *
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Return the "directory" path for the object
     *
     * @return string
     */
    public function getPath()
    {
        return dirname($this->absPath);
    }

    /**
     * Return the name of the object
     *
     * @return string
     */
    public function getName()
    {
        return basename($this->name);
    }
}
