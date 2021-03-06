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

use Vipkwd\VarDumper\Properties\Properties;
use Vipkwd\VarDumper\Properties\PropertyInterface;

/**
 * @internal
 */
abstract class AbstractObjectDumper extends AbstractDumper
{
    /**
     * @param $object
     *
     * @return PropertyInterface[]
     */
    protected function getProperties($object)
    {
        $properties = new Properties($object);

        return $properties->getProperties();
    }

    protected function getClassName($object)
    {
        $class = \get_class($object);

        // @see https://github.com/facebook/hhvm/issues/7868
        // @codeCoverageIgnoreStart
        if (\defined('HHVM_VERSION') && $object instanceof \Closure) {
            $class = 'Closure';
        }

        // @codeCoverageIgnoreEnd

        return $class;
    }
}
