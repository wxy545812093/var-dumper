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
final class Hasher72 extends BaseHasher
{
    public function getHashId($object)
    {
        $this->validateObject($object);

        return (string)\spl_object_id($object);
    }
}
