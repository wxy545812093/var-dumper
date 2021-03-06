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

use Vipkwd\VarDumper\Helpers\Container;
use Vipkwd\VarDumper\Helpers\KeyValuePrinter;
use Vipkwd\VarDumper\Helpers\Strings;
use Vipkwd\VarDumper\Strings\LinePart;
use Vipkwd\VarDumper\Strings\PartInterface;
use Vipkwd\VarDumper\Strings\Parts;

/**
 * @internal
 */
final class ArrayBigDumper extends AbstractDumper
{
    /**
     * @param array     $array
     * @param Container $container
     *
     * @return PartInterface
     */
    public static function dumpBody(array $array, Container $container)
    {
        $config = $container->getConfig();
        $dumper = $container->getDumper();

        $indent = $config->getIndent();
        $limit = $config->getMaxChildren();
        $printer = new KeyValuePrinter();

        $result = new Parts();

        foreach ($array as $key => $value) {
            $key = Strings::prepareArrayKey($key);
            $subPart = $dumper->dumpAsPart($value);

            if (!$subPart->isMultiLine()) {
                $printer->add("[{$key}] => ", $subPart, \mb_strlen("[{$key}] => "));
            } else {
                if ($flushed = $printer->flush()) {
                    $result->appendPart($flushed);
                }
                $subPart->addIndent($indent);
                $result->appendPart(new LinePart("[{$key}] =>"));
                $result->appendPart($subPart);
            }

            if (!--$limit) {
                if ($flushed = $printer->flush()) {
                    $result->appendPart($flushed);
                }
                if (\count($array) > $config->getMaxChildren()) {
                    $result->appendPart(new LinePart('(...)'));
                }
                break;
            }
        }

        if ($flushed = $printer->flush()) {
            $result->appendPart($flushed);
        }

        return $result;
    }

    public function supports($var)
    {
        return \is_array($var);
    }

    public function dump($array)
    {
        $count = \count($array);

        $result = new Parts();
        $header = new LinePart('array(' . $count . ') {');
        $result->appendPart($header);

        if ($count > 0) {
            $body = static::dumpBody($array, $this->container);
            $body->addIndent($this->container->getConfig()->getIndent());
            $result->appendPart($body);
            $result->appendPart(new LinePart('}'));
        } else {
            $header->append('}');
        }

        return $result;
    }
}
