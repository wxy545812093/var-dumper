<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Config;

/**
 * @internal
 */
abstract class AbstractConfig
{
    protected $maxChildren;

    protected $maxDepth;

    protected $maxStringLen;

    protected $maxLineLen;

    protected $indent;

    protected $maxFileNameDepth;

    public function __construct($maxChildren, $maxDepth, $maxStringLen, $maxLineLen, $indent, $maxFileNameDepth)
    {
        $this->maxChildren = $maxChildren;
        $this->maxDepth = $maxDepth;
        $this->maxStringLen = $maxStringLen;
        $this->maxLineLen = $maxLineLen;
        $this->indent = $indent;
        $this->maxFileNameDepth = $maxFileNameDepth;
    }

    public function getMaxChildren()
    {
        return $this->maxChildren;
    }

    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    public function getMaxStringLength()
    {
        return $this->maxStringLen;
    }

    public function getMaxLineLength()
    {
        return $this->maxLineLen;
    }

    public function getIndent()
    {
        return $this->indent;
    }

    public function getMaxFileNameDepth()
    {
        return $this->maxFileNameDepth;
    }
}
