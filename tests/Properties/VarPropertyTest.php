<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Properties;

use Vipkwd\VarDumper\BaseTestCase;

/**
 * @internal
 */
final class VarPropertyTest extends BaseTestCase
{
    /**
     * @dataProvider             providerInvalidConstructor
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid value of $visibility!
     */
    public function testInvalidConstructor()
    {
        $reflection = new \ReflectionClass('Vipkwd\VarDumper\Properties\VarProperty');
        $reflection->newInstanceArgs(\func_get_args());
    }

    public function providerInvalidConstructor()
    {
        return array(
            array('name', 'value', false, \get_class($this)),
            array('name', 'value', new \stdClass(), \get_class($this)),
        );
    }
}
