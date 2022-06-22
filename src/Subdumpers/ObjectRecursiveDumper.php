<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Subdumpers;

use Vipkwd\VarDumper\Strings\LinePart;

/**
 * @internal
 */
final class ObjectRecursiveDumper extends AbstractObjectDumper
{
    public function supports($var)
    {
        return \is_object($var) && $this->container->getReferences()->in($var);
    }

    public function dump($var)
    {
        return new LinePart(
            'RECURSIVE object(' . $this->getClassName($var) . ') #' . $this->container->getHasher()->getHashId($var)
        );
    }
}
