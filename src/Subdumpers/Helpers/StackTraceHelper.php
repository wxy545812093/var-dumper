<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Subdumpers\Helpers;

use Vipkwd\VarDumper\Helpers\Container;
use Vipkwd\VarDumper\Helpers\FileNameDecorator;
use Vipkwd\VarDumper\Helpers\KeyValuePrinter;
use Vipkwd\VarDumper\Strings\LinePart;
use Vipkwd\VarDumper\Strings\PartInterface;
use Vipkwd\VarDumper\Strings\Parts;

/**
 * @internal
 */
final class StackTraceHelper
{
    /**
     * @param array     $trace
     * @param Container $container
     *
     * @return PartInterface
     */
    public static function dumpStackTraceAsPart(array $trace, Container $container)
    {
        // @codeCoverageIgnoreStart
        if (empty($trace)) {
            return new LinePart('[]');
        }
        // @codeCoverageIgnoreEnd

        $result = new Parts();
        $limit = $container->getConfig()->getMaxChildren();
        foreach ($trace as $key => $step) {
            $result->appendPart(self::dumpStep($step, $container, $key));
            if (!--$limit && \count($trace) > $container->getConfig()->getMaxChildren()) {
                $result->appendPart(new LinePart('(...)'));
                break;
            }
        }

        return $result;
    }

    /**
     * @param array     $step
     * @param Container $container
     * @param int       $index
     *
     * @return PartInterface
     */
    private static function dumpStep(array $step, Container $container, $index)
    {
        $header = new LinePart(($index + 1) . '.');
        if (isset($step['file']) && isset($step['line'])) {
            $header->append(new LinePart(' ' . FileNameDecorator::decorateFileName($step['file'], $container->getConfig()->getMaxFileNameDepth()) . ':' . $step['line']));
        }

        $addedSpace = false;
        $hasFunction = false;
        foreach (array('class', 'type', 'function') as $name) {
            if (isset($step[$name])) {
                $hasFunction = true;
                if (!$addedSpace) {
                    $header->append(' ');
                    $addedSpace = true;
                }
                $header->append($step[$name]);
            }
        }
        if ($hasFunction) {
            $header->append('(');
        }

        $args = self::getArgs($step);

        if (empty($args)) {
            if ($hasFunction) {
                $header->append(')');
            }

            return $header;
        }

        $argsPart = self::dumpArgs($args, $container);
        $argsPart->addIndent($container->getConfig()->getIndent());

        $result = new Parts();
        $result->appendPart($header);
        $result->appendPart($argsPart);
        $result->appendPart(new LinePart(')'));

        return $result;
    }

    /**
     * @param array     $args
     * @param Container $container
     *
     * @return PartInterface
     */
    private static function dumpArgs(array $args, Container $container)
    {
        $argsPart = new Parts();
        $printer = new KeyValuePrinter();
        $limit = $container->getConfig()->getMaxChildren();
        foreach ($args as $data) {
            list($key, $arg) = $data;
            $argDumped = $container->getDumper()->dumpAsPart($arg);

            if ($argDumped->isMultiLine()) {
                if ($toPrint = $printer->flush()) {
                    $argsPart->appendPart($toPrint);
                }

                $argPart = new Parts();
                $argPart->appendPart(new LinePart($key . ':'));

                $argDumped->addIndent($container->getConfig()->getIndent());
                $argPart->appendPart($argDumped);

                $argsPart->appendPart($argPart);
            } else {
                $printer->add($key . ': ', $argDumped, \mb_strlen($key . ': '));
            }

            if (!--$limit && \count($args) > $container->getConfig()->getMaxChildren()) {
                if ($toPrint = $printer->flush()) {
                    $argsPart->appendPart($toPrint);
                }

                $argsPart->appendPart(new LinePart('(...)'));
                break;
            }
        }

        if ($toPrint = $printer->flush()) {
            $argsPart->appendPart($toPrint);
        }

        return $argsPart;
    }

    private static function getArgs($stackTraceRow)
    {
        if (empty($stackTraceRow['args'])) {
            return array();
        }

        $result = array();
        $function = self::getFunctionReflection($stackTraceRow);
        $params = $function ? $function->getParameters() : array();
        $args = $stackTraceRow['args'];

        $k = 0;
        while (\count($args) > 0) {
            /** @var null|\ReflectionParameter $param */
            $param = \array_shift($params);
            ++$k;

            if (null !== $param && self::isVariadic($param)) {
                $arg = $args;
                $args = array();
            } else {
                $arg = \array_shift($args);
            }

            $result[] = array(
                null !== $param ? $param->getName() : "arg{$k}",
                $arg,
            );
        }

        return $result;
    }

    /**
     * @param array $stackTraceRow
     *
     * @return null|\ReflectionFunctionAbstract
     */
    private static function getFunctionReflection(array $stackTraceRow)
    {
        // @codeCoverageIgnoreStart
        if (empty($stackTraceRow['function'])) {
            return null;
        }
        // @codeCoverageIgnoreEnd

        $fn = $stackTraceRow['function'];

        if (!empty($stackTraceRow['class'])) {
            $class = $stackTraceRow['class'];
            // @codeCoverageIgnoreStart
            if (!\class_exists($class)) {
                return null;
            }
            // @codeCoverageIgnoreEnd

            $reflectionClass = new \ReflectionClass($class);
            if (!$reflectionClass->hasMethod($fn)) {
                return null;
            }

            return $reflectionClass->getMethod($fn);
        }

        if (\function_exists($fn)) {
            return new \ReflectionFunction($fn);
        }

        return null;
    }

    private static function isVariadic(\ReflectionParameter $parameter)
    {
        return \method_exists($parameter, 'isVariadic') && $parameter->isVariadic();
    }
}
