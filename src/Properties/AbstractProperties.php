<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Properties;

/**
 * @internal
 */
abstract class AbstractProperties implements PropertiesInterface
{
    protected $object;

    protected function getDeclaredProperties()
    {
        $reflection = new \ReflectionObject($this->object);
        $result = array();

        if ($reflection->hasMethod('__get')) {
            foreach (\array_keys(\get_object_vars($this->object)) as $name) {
                $result[] = $reflection->getProperty($name);
            }

            return $result;
        }

        foreach ($reflection->getProperties() as $property) {
            $continue = false;
            \set_error_handler(
                function () use (&$continue) {
                    $continue = true;
                },
                \E_NOTICE
            );
            $property->setAccessible(true);
            $property->getValue($this->object);
            \restore_error_handler();
            if ($continue) {
                continue;
            }

            $result[] = $property;
        }

        return $result;
    }
}
