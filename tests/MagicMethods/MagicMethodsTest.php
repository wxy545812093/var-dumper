<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\MagicMethods;

use Vipkwd\VarDumper\BaseTestCase;
use Vipkwd\VarDumper\LightVarDumper;
use Vipkwd\VarDumper\Properties\Properties;
use Vipkwd\VarDumper\Properties\PropertyInterface;

/**
 * @internal
 */
final class MagicMethodsTest extends BaseTestCase
{
    public function testDump()
    {
        $dumper = new LightVarDumper();
        $object = new RemovedProperty();

        $expected
            = <<<'EXPECTED'
(2) {
    private $a => “a”
    private $b => “b”
}
EXPECTED;
        $this->assertContains($expected, $dumper->dumpAsString($object));

        $parts = array(
            'private $b =>',
        );
        if (!\defined('HHVM_VERSION') && \version_compare(\PHP_VERSION, '7.0') >= 0) {
            $parts[] = '(1) {';
        } else {
            $parts[] = '(2) {';
            $parts[] = 'private $a => NULL';
        }
        $object->without('a');
        foreach ($parts as $part) {
            $this->assertContains($part, $dumper->dumpAsString($object));
        }

        $object->c = 'c';
        $parts = array(
            'private $b =>',
            '$c =>',
        );
        if (!\defined('HHVM_VERSION') && \version_compare(\PHP_VERSION, '7.0') >= 0) {
            $parts[] = '(2) {';
        } else {
            $parts[] = '(3) {';
            $parts[] = 'private $a => NULL';
        }
        foreach ($parts as $part) {
            $this->assertContains($part, $dumper->dumpAsString($object));
        }

        $getter = new GetterObject();
        $this->assertContains('(0) {}', $dumper->dumpAsString($getter));
    }

    /**
     * @dataProvider providerAbstractProperties
     *
     * @param       $object
     * @param array $expectedProperties
     * @param array $diff
     */
    public function testAbstractProperties($object, array $expectedProperties, $diff)
    {
        $reader = new Properties($object);

        if (!\defined('HHVM_VERSION') && \version_compare(\PHP_VERSION, '7.0') >= 0) {
            $this->assertSame(\count($expectedProperties), \count($reader->getProperties()));
        } else {
            $this->assertSame(\count($expectedProperties) + \count($diff), \count($reader->getProperties()));
        }

        foreach ($expectedProperties as $expectedProperty) {
            $contains = false;
            foreach ($reader->getProperties() as $property) {
                /** @var PropertyInterface $property */
                if ($contains = $property->getName() === $expectedProperty) {
                    $contains = true;
                    break;
                }
            }
            $this->assertTrue($contains);
        }
    }

    public function providerAbstractProperties()
    {
        return array(
            array(new RemovedProperty(), array('a', 'b'), array()),
            array(new GetterObject(), array(), array()),
            array(RemovedProperty::createWithout(array('a')), array('b'), array('a')),
            array(RemovedProperty::createWithout(array('a'))->with('c'), array('b', 'c'), array('a')),
            array(RemovedProperty::createWithout(array('a', 'b')), array(), array('a', 'b')),
        );
    }
}
