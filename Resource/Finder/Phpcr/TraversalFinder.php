<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Resource\Finder\Phpcr;

use Puli\Resource\ResourceInterface;
use Symfony\Cmf\Bundle\ResourceBundle\Resource\FinderInterface;
use Puli\Resource\Collection\ResourceCollection;
use Symfony\Cmf\Bundle\ResourceBundle\Resource\Finder\SelectorParser;
use PHPCR\NodeInterface;
use PHPCR\SessionInterface;

/**
 * Resource for objects, intended for use with content repositories
 * documents but could be used in other contexts (i.e. Doctrine entities).
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class TraversalFinder implements FinderInterface
{
    /**
     * @var SelectorParser
     */
    private $parser;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param SelectorParser
     */
    public function __construct(SessionInterface $session, SelectorParser $parser)
    {
        $this->parser = $parser;
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function find($selector)
    {
        var_dump(': ' .$selector);
        $segments = $this->parser->parse($selector);

        return $this->traverse($segments);
    }

    private function traverse($segments, $basePath = null)
    {
        $path = '';
        $ret = array();
        foreach ($segments as $element => $bitmask) {

            if ($bitmask & SelectorParser::T_STATIC) {
                $path .= '/' . $element;
                continue;
            }

            if ($bitmask & SelectorParser::T_PATTERN) {
                $children = $this->getNodes($path, $element);

                foreach ($children as $child) {
                    // do something
                }
            }
        }

        return $ret;
    }

    private function getNode($absPath)
    {
        if ($absPath === '') {
            $absPath = '/';
        }

        $node = null;
        try {
            $node = $this->session->getNode($absPath);
        } catch (\PHPCR\PathNotFoundException $e) {
            // nothing
        }

        return $node;
    }

    private function getNodes($path, $pattern)
    {
        $node = $this->getNode($path);

        if (null === $node) {
            return array();
        }

        return $node->getNodes($pattern);
    }
}
