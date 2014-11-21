<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Resource\Finder;

use Puli\Resource\ResourceInterface;
use Symfony\Cmf\Bundle\ResourceBundle\Resource\FinderInterface;
use Puli\Resource\Collection\ResourceCollection;

/**
 * Parser for selector patterns
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class SelectorParser
{
    const T_STATIC = 1;
    const T_PATTERN = 2;
    const T_LAST = 4;

    /**
     * Parse the given selector.
     *
     * Returns an associative array of path elements to bitmasks.
     *
     * @return array
     */
    public function parse($selector)
    {
        if ('/' === $selector) {
            return array();
        }

        if ('/' !== substr($selector, 0, 1)) {
            throw new \InvalidArgumentException(sprintf(
                'Path "%s" must be absolute',
                $selector
            ));
        }

        $selector = substr($selector, 1);

        if (false === strpos($selector, '/')) {
            return array(
                $selector => self::T_STATIC | self::T_LAST
            );
        }

        $segments = array();
        $elements = explode('/', $selector);

        foreach ($elements as $index => $element) {
            if ($this->containsGlob($element)) {
                $flags = self::T_PATTERN;
            } else {
                $flags = self::T_STATIC;
            }

            if ($index === (count($elements) - 1)) {
                $flags = $flags | self::T_LAST;
            }

            $segments[$element] = $flags;
        }

        return $segments;
    }

    private function containsGlob($string)
    {
        if (false === strpos($string, '?') && false === strpos($string, '*')) {
            return false;
        }

        return true;
    }
}

