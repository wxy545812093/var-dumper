<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Vipkwd\VarDumper\LightVarDumper;

require __DIR__ . \DIRECTORY_SEPARATOR . 'init.php';

/**
 * @internal
 *
 * @see http://php.net/manual/en/language.oop5.magic.php#object.debuginfo
 */
class MyClass
{
    public function __debugInfo()
    {
        return array(
            'key' => 'value',
            'some' => 'output',
        );
    }
}

$dumper = new LightVarDumper();
$dumper->dump(new MyClass());

/*

Output:

object(MyClass) #1 (2) {[
    [key] =>  “value”
    [some] => “output”
]}

*/
