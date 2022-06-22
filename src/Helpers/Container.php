<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Helpers;

use Vipkwd\VarDumper\Config\AbstractConfig;
use Vipkwd\VarDumper\Objects\HasherFactory;
use Vipkwd\VarDumper\Objects\HasherInterface;
use Vipkwd\VarDumper\Subdumpers\SubdumpersCollection;

/**
 * @internal
 */
final class Container
{
    private $references;

    private $depth;

    private $config;

    private $dumper;

    private static $hasher;

    public function __construct(AbstractConfig $config, SubdumpersCollection $dumper)
    {
        $this->config = $config;
        $this->dumper = $dumper;
    }

    /**
     * @return AbstractConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Stack
     */
    public function getReferences()
    {
        return $this->references ?: $this->references = new Stack();
    }

    /**
     * @return IntValue
     */
    public function getDepth()
    {
        return $this->depth ?: $this->depth = new IntValue();
    }

    /**
     * @return SubdumpersCollection
     */
    public function getDumper()
    {
        return $this->dumper;
    }

    /**
     * @return HasherInterface
     */
    public function getHasher()
    {
        return self::$hasher ?: self::$hasher = HasherFactory::create();
    }
}
