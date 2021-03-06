<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\LightVarDumperProviders;

/**
 * @internal
 */
final class TestObject extends TestParent
{
    public static $static = 'static value';

    public $public;

    protected $protected;

    public function setProtected($value)
    {
        $this->protected = $value;
    }
}
