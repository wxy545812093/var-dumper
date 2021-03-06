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

use Vipkwd\VarDumper\BaseTestCase;

/**
 * @internal
 */
final class ReflectionPropertyTest extends BaseTestCase
{
    private $testProperty;

    /**
     * @dataProvider             providerInvalidConstructor
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument $object is not an object!
     */
    public function testInvalidConstructor()
    {
        $reflection = new \ReflectionClass('Vipkwd\VarDumper\Properties\ReflectionProperty');
        $reflection->newInstanceArgs(\func_get_args());
    }

    public function providerInvalidConstructor()
    {
        $reflection = new \ReflectionProperty($this, 'testProperty');

        return array(
            array($reflection, false),
            array($reflection, 1),
            array($reflection, \get_class($this)),
        );
    }

    /**
     * @dataProvider providerGetDeclaringClass
     *
     * @param ReflectionProperty $property
     * @param string             $expectedClass
     */
    public function testGetDeclaringClass(ReflectionProperty $property, $expectedClass)
    {
        $this->assertSame($expectedClass, $property->getDeclaringClass());
    }

    public function providerGetDeclaringClass()
    {
        $childClass = \get_class(new TestChild());
        $parentClass = \get_class(new TestParent());

        return array(
            array(new ReflectionProperty(new \ReflectionProperty($childClass, 'foo'), new TestChild()), $parentClass),
        );
    }
}
