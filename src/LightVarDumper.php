<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper;

use Vipkwd\VarDumper\Config\EditableConfig;
use Vipkwd\VarDumper\Helpers\Strings;
use Vipkwd\VarDumper\Subdumpers\SubdumpersCollection;

final class LightVarDumper extends InternalVarDumper
{
    const DEFAULT_MAX_CHILDREN = 20;
    const DEFAULT_MAX_STRING_LENGTH = 200;
    const DEFAULT_MAX_LINE_LENGTH = 130;
    const DEFAULT_MAX_DEPTH = 5;
    const DEFAULT_MAX_FILENAME_DEPTH = 3;
    const DEFAULT_INDENT = '    ';

    /**
     * @var EditableConfig
     */
    private $config;

    /**
     * @var SubdumpersCollection
     */
    private $subdumper;

    /**
     * {@inheritdoc}
     */
    public function __construct($displayPlaceInCode = false, $stepShift = 0)
    {
        parent::__construct($displayPlaceInCode, $stepShift);

        $this->config = $config = new Config\EditableConfig(
            static::DEFAULT_MAX_CHILDREN,
            static::DEFAULT_MAX_DEPTH,
            static::DEFAULT_MAX_STRING_LENGTH,
            static::DEFAULT_MAX_LINE_LENGTH,
            static::DEFAULT_INDENT,
            static::DEFAULT_MAX_FILENAME_DEPTH
        );

        $this->subdumper = new SubdumpersCollection($this->config);
    }

    public function dump($var)
    {
        if ($this->displayPlaceInCode) {
            echo $this->dumpPlaceInCodeAsString(0);
        }

        echo $this->subdumper->dumpAsPart($var), "\n";
    }

    public function dumpAsString($var)
    {
        $prefix = $this->displayPlaceInCode
            ? $this->dumpPlaceInCodeAsString(0)
            : '';

        return $prefix . $this->subdumper->dumpAsPart($var) . "\n";
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setMaxDepth($limit)
    {
        $limit = (int)$limit;
        if ($limit < 1) {
            throw new \InvalidArgumentException('Limit must be greater or equal 1');
        }

        $this->config->setMaxDepth($limit);

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setMaxStringLength($limit)
    {
        $limit = (int)$limit;
        if ($limit < 5) {
            throw new \InvalidArgumentException('Limit must be greater or equal 5');
        }

        $this->config->setMaxStringLength($limit);

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setMaxLineLength($limit)
    {
        $limit = (int)$limit;
        if ($limit < 5) {
            throw new \InvalidArgumentException('Limit must be greater or equal 5');
        }

        $this->config->setMaxLineLength($limit);

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setMaxChildren($limit)
    {
        $limit = (int)$limit;
        if ($limit < 1) {
            throw new \InvalidArgumentException('Limit must be greater or equal 1');
        }

        $this->config->setMaxChildren($limit);

        return $this;
    }

    /**
     * @param string $indent
     *
     * @return $this
     */
    public function setIndent($indent)
    {
        $indent = (string)$indent;
        $len = \mb_strlen($indent);
        if ($len < 1) {
            throw new \InvalidArgumentException('Length of indent must be greater or equal 1');
        }
        foreach (\array_keys(Strings::$replaceChars) as $char) {
            if (false !== \mb_strpos($indent, $char)) {
                throw new \InvalidArgumentException('Indent cannot contain white characters except spaces');
            }
        }
        $this->config->setIndent($indent);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxFileNameDepth($depth)
    {
        $this->config->setMaxFileNameDepth((int)$depth);

        return parent::setMaxFileNameDepth($depth);
    }
}
