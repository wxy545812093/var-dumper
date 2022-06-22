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
final class ObjectTooDepthDumper extends AbstractObjectDumper
{
    public function supports($var)
    {
        return \is_object($var)
            && $this->container->getDepth()->getValue() === $this->container->getConfig()->getMaxDepth();
    }

    public function dump($object)
    {
        $class = $this->getClassName($object);
        $properties = $this->getProperties($object);

        $result = 'object(' . $class . ') #' . $this->container->getHasher()->getHashId($object) . ' (' . \count($properties) . ') {';
        $result .= \count($properties) ? '...' : '';
        $result .= '}';

        return new LinePart($result);
    }
}
