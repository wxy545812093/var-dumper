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

/**
 * @internal
 */
final class IntValue
{
    private $value;

    public function __construct($value = 0)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function incr()
    {
        ++$this->value;
    }

    public function decr()
    {
        --$this->value;
    }
}
