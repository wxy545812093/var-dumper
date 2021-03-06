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
final class Stack
{
    private $array = array();

    public function pop()
    {
        return \array_pop($this->array);
    }

    public function push($item)
    {
        return \array_push($this->array, $item);
    }

    public function in($element)
    {
        return \in_array($element, $this->array, true);
    }
}
