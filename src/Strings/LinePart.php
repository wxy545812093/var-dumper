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
final class LinePart implements PartInterface
{
    private $indent = '';

    private $value;

    public function isMultiLine()
    {
        return false;
    }

    /**
     * @param string $value cannot contain \n character
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $value cannot contain \n character
     */
    public function append($value)
    {
        $this->value .= $value;
    }

    public function addIndent($indent)
    {
        $this->indent .= $indent;
    }

    public function __toString()
    {
        return $this->indent . $this->value;
    }
}
