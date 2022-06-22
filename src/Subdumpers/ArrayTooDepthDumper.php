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
final class ArrayTooDepthDumper extends AbstractDumper
{
    public function supports($var)
    {
        return \is_array($var)
            && $this->container->getDepth()->getValue() === $this->container->getConfig()->getMaxDepth();
    }

    public function dump($var)
    {
        $c = \count($var);

        return new LinePart('array(' . $c . ') {' . ($c ? '...' : '') . '}');
    }
}
