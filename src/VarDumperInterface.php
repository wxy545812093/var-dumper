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

interface VarDumperInterface
{
    public function dump($var);

    /**
     * @param mixed $var
     *
     * @return string
     */
    public function dumpAsString($var);
}
