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

use Vipkwd\VarDumper\InternalVarDumper;
use Vipkwd\VarDumper\Strings\LinePart;
use Vipkwd\VarDumper\Strings\Parts;

/**
 * @internal
 */
final class NativeDumper implements SubdumperInterface
{
    public function supports($var)
    {
        return true;
    }

    public function dump($var)
    {
        $internal = new InternalVarDumper();
        $rawDump = \rtrim($internal->dumpAsString($var), "\n");
        $parts = new Parts();
        foreach (\explode("\n", $rawDump) as $part) {
            $parts->appendPart(new LinePart($part));
        }

        return $parts;
    }
}
