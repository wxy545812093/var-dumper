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

use Vipkwd\VarDumper\Strings\PartInterface;

/**
 * @internal
 */
interface SubdumperInterface
{
    /**
     * @param $var
     *
     * @return bool
     */
    public function supports($var);

    /**
     * @throws VarNotSupportedException
     *
     * @return PartInterface
     */
    public function dump($var);
}
