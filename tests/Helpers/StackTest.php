<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Helpers;

use Vipkwd\VarDumper\BaseTestCase;

/**
 * @internal
 */
final class StackTest extends BaseTestCase
{
    /**
     * @dataProvider providerIn
     *
     * @param Stack $stack
     * @param       $element
     * @param       $expected
     */
    public function testIn(Stack $stack, $element, $expected)
    {
        $this->assertInternalType('bool', $expected);
        $this->assertSame($expected, $stack->in($element));
    }

    public function providerIn()
    {
        $stack = new Stack();
        $a = '1';
        $b = 2;
        $stack->push($a);
        $stack->push($b);

        return array(
            array($stack, 1, false),
            array($stack, '1', true),
            array($stack, 2, true),
            array($stack, '2', false),
        );
    }
}
