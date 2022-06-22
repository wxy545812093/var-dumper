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
interface HasherInterface
{
    /**
     * Returns hash id for given object.
     *
     * @param object $object
     *
     * @return string
     */
    public function getHashId($object);
}
