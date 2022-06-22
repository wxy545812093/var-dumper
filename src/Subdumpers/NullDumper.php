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
final class NullDumper implements SubdumperInterface
{
    public function supports($var)
    {
        return null === $var;
    }

    public function dump($var)
    {
        return new LinePart('NULL');
    }
}
