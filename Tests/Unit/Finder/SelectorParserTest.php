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

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Bundle\ResourceBundle\Resource\Repository\PhpcrOdmRepository;

class SelectorParserTest extends ProphecyTestCase
{
    /**
     * @var SelectorParser
     */
    private $parser;

    public function setUp()
    {
        $this->parser = new SelectorParser();
    }

    public function provideParse()
    {
        return array(
            array(
                '/',
                array(
                ),
            ),
            array(
                '/foo',
                array(
                    'foo' => SelectorParser::T_STATIC | SelectorParser::T_LAST,
                ),
            ),
            array(
                '/foo/bar',
                array(
                    'foo' => SelectorParser::T_STATIC,
                    'bar' => SelectorParser::T_STATIC | SelectorParser::T_LAST,
                ),
            ),
            array(
                '/*/bar',
                array(
                    '*' => SelectorParser::T_PATTERN,
                    'bar' => SelectorParser::T_STATIC | SelectorParser::T_LAST,
                ),
            ),
            array(
                '/foo?/bar',
                array(
                    'foo?' => SelectorParser::T_PATTERN,
                    'bar' => SelectorParser::T_STATIC | SelectorParser::T_LAST,
                ),
            ),
            array(
                '/foo?/bar/baz*',
                array(
                    'foo?' => SelectorParser::T_PATTERN,
                    'bar' => SelectorParser::T_STATIC,
                    'baz*' => SelectorParser::T_PATTERN | SelectorParser::T_LAST,
                ),
            ),
        );
    }

    /**
     * @dataProvider provideParse
     */
    public function testParse($path, $expected)
    {
        $res = $this->parser->parse($path);
        $this->assertSame($res, $expected);
    }
}
