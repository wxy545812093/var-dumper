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

/**
 * @internal
 */
final class ArrayObject extends \ArrayObject
{
    private $privateProperty = 'private value';

    public function getArrayCopy()
    {
        $this->throwForbidden();
    }

    public function getIteratorClass()
    {
        $this->throwForbidden();
    }

    private function throwForbidden()
    {
        throw new \Exception('Forbidden!');
    }
}
