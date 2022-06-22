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
use Vipkwd\VarDumper\Properties\PropertyInterface;
use Vipkwd\VarDumper\Strings\LinePart;
use Vipkwd\VarDumper\Strings\Parts;

/**
 * @internal
 */
final class ObjectBigDumper extends AbstractObjectDumper
{
    /**
     * @param PropertyInterface[] $properties
     * @param Container           $container
     *
     * @return Parts
     */
    public static function dumpProperties($properties, Container $container)
    {
        $limit = $container->getConfig()->getMaxChildren();
        $printer = new KeyValuePrinter();
        $indent = $container->getConfig()->getIndent();

        $result = new Parts();

        foreach ($properties as $property) {
            $propName = Strings::prepareArrayKey($property->getName());
            $key = self::getTextTypePrefix($property) . '$' . $propName;

            $subPart = $container->getDumper()->dumpAsPart($property->getValue());
            if (!$subPart->isMultiLine()) {
                $printer->add("{$key} => ", $subPart, \mb_strlen("{$key} => "));
            } else {
                if ($flushed = $printer->flush()) {
                    $result->appendPart($flushed);
                }
                $subPart->addIndent($indent);
                $result->appendPart(new LinePart("{$key} =>"));
                $result->appendPart($subPart);
            }

            if (!--$limit) {
                if ($flushed = $printer->flush()) {
                    $result->appendPart($flushed);
                }
                if (\count($properties) > $container->getConfig()->getMaxChildren()) {
                    $result->appendPart(new LinePart('(...)'));
                }
                break;
            }
        }
        if ($flushed = $printer->flush()) {
            $result->appendPart($flushed);
        }

        $result->addIndent($indent);

        return $result;
    }

    public function supports($var)
    {
        return \is_object($var);
    }

    public function dump($object)
    {
        $properties = $this->getProperties($object);
        $class = $this->getClassName($object);

        $count = \count($properties);
        $result = new Parts();
        $header = new LinePart('object(' . $class . ') #' . $this->container->getHasher()->getHashId($object) . ' (' . $count . ') {');
        $result->appendPart($header);
        if ($count > 0) {
            $result->appendPart(static::dumpProperties($properties, $this->container));
            $result->appendPart(new LinePart('}'));
        } else {
            $header->append('}');
        }

        return $result;
    }

    private static function getTextTypePrefix(PropertyInterface $property)
    {
        if ($property->isVirtual()) {
            return '';
        }

        $suffix = $property->isStatic() ? 'static ' : '';

        if ($property->isPublic()) {
            return 'public ' . $suffix;
        }

        if ($property->isProtected()) {
            return 'protected ' . $suffix;
        }

        return 'private ' . $suffix;
    }
}
