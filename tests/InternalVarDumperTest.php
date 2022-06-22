<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper;

/**
 * @internal
 */
final class InternalVarDumperTest extends BaseTestCase
{
    /**
     * @dataProvider providerAll
     *
     * @param mixed  $value
     * @param string $dump
     */
    public function testAll($value, $dump)
    {
        $dumper = new InternalVarDumper();
        $this->assertSame($dump, $dumper->dumpAsString($value));
        $this->expectOutputString($dump);
        $dumper->dump($value);
    }

    public function providerAll()
    {
        return array(
            array(1, "int(1)\n"),
            array(false, "bool(false)\n"),
            array(null, "NULL\n"),
        );
    }
}
