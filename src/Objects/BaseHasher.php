<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Objects;

/**
 * @internal
 */
abstract class BaseHasher implements HasherInterface
{
    protected function validateObject($object)
    {
        if (!\is_object($object)) {
            throw new \InvalidArgumentException(\sprintf('%s::getHashId expects parameter 1 to be object, %s given', \get_class($this), \gettype($object)));
        }
    }
}
