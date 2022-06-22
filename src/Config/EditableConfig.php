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
final class EditableConfig extends AbstractConfig
{
    public function setMaxChildren($limit)
    {
        $this->maxChildren = $limit;

        return $this;
    }

    public function setMaxDepth($limit)
    {
        $this->maxDepth = $limit;

        return $this;
    }

    public function setMaxStringLength($limit)
    {
        $this->maxStringLen = $limit;

        return $this;
    }

    public function setMaxLineLength($limit)
    {
        $this->maxLineLen = $limit;

        return $this;
    }

    public function setIndent($indent)
    {
        $this->indent = $indent;

        return $this;
    }

    public function setMaxFileNameDepth($maxFileNameDepth)
    {
        $this->maxFileNameDepth = $maxFileNameDepth;

        return $this;
    }
}
