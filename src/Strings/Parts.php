<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Strings;

/**
 * @internal
 */
final class Parts implements PartsInterface
{
    /**
     * @var PartInterface[]
     */
    private $parts = array();

    public function isMultiLine()
    {
        return \count($this->parts) > 1;
    }

    public function appendPart(PartInterface $part)
    {
        $this->parts[] = $part;
    }

    public function addIndent($indent)
    {
        foreach ($this->parts as $part) {
            $part->addIndent($indent);
        }
    }

    public function __toString()
    {
        return \implode("\n", $this->parts);
    }
}
