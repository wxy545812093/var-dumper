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
final class Hasher extends BaseHasher
{
    private static $hashes = array();

    private static $counter = 0;

    public function getHashId($object)
    {
        $this->validateObject($object);

        $splHash = \spl_object_hash($object);

        if (!isset(self::$hashes[$splHash])) {
            self::$hashes[$splHash] = ++self::$counter;
        }

        return (string)self::$hashes[$splHash];
    }
}
