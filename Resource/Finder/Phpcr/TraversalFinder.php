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
        if ($selector == '/') {
            return array($this->getNode(array()));
        }

        $segments = $this->parser->parse($selector);

        $ret = array();
        $this->traverse(null, $segments, $ret);

        return $ret;
    }

    private function traverse($node = null, $segments, &$ret = array())
    {
        $path = array();

        if (null !== $node) {
            $path = explode('/', substr($node->getPath(), 1));
        }

        do {
            list($element, $bitmask) = array_shift($segments);

            if ($bitmask & SelectorParser::T_STATIC) {
                $path[] = $element;

                if ($bitmask & SelectorParser::T_LAST) {
                    if ($node = $this->getNode($path)) {
                        $ret[] = $node;
                        break;
                    }
                }
            }

            if ($bitmask & SelectorParser::T_PATTERN) {
                $parentNode = $this->getNode($path);

                $children = $parentNode->getNodes($element);

                foreach ($children as $child) {
                    if ($bitmask & SelectorParser::T_LAST) {
                        $ret[] = $child;
                    } else {
                        $this->traverse($child, $segments, $ret);
                    }
                }

                return;
            }
        } while (count($segments));
    }

    private function getNode(array $path)
    {
        $absPath = '/' . implode('/', $path);
        $node = null;

        try {
            $node = $this->session->getNode($absPath);
        } catch (\PHPCR\PathNotFoundException $e) {
            // nothing
        }

        return $node;
    }
}
