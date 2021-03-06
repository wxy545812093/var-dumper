<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Objects;

use Vipkwd\VarDumper\BaseTestCase;

final class HasherTest extends BaseTestCase
{
    public function testHash()
    {
        $hasher = HasherFactory::create();

        $object1 = new \stdClass();
        $object2 = clone $object1;
        $object3 = new \stdClass();

        $hash1 = $hasher->getHashId($object1);
        $hash2 = $hasher->getHashId($object2);
        $hash3 = $hasher->getHashId($object3);

        $this->assertNotEquals($hash1, $hash2, $this->getHashMessage($object1, $object2));
        $this->assertNotEquals($hash2, $hash3, $this->getHashMessage($object2, $object3));
        $this->assertNotEquals($hash1, $hash3, $this->getHashMessage($object1, $object3));

        $object1->property = 'value';
        $this->assertEquals($hash1, $hasher->getHashId($object1));

        // "Once the object is destroyed, its hash may be reused for other objects."
        // @see http://php.net/manual/en/function.spl-object-hash.php
        // $this->assertEquals($hasher->getHashId(new \stdClass()), $hasher->getHashId(new \stdClass()));
    }

    /**
     * @dataProvider providerInvalidException
     * @expectedException \InvalidArgumentException
     *
     * @param $object
     */
    public function testInvalidArgument($object)
    {
        $hasher = HasherFactory::create();
        $hasher->getHashId($object);
    }

    public function providerInvalidException()
    {
        return array(
            array(1),
            array(null),
            array(false),
            array('hello'),
            array(\tmpfile()),
        );
    }

    /**
     * @param $object1
     * @param $object2
     *
     * @return string
     */
    private function getHashMessage($object1, $object2)
    {
        $hash1 = \spl_object_hash($object1);
        $hash2 = \spl_object_hash($object2);

        $result = \sprintf(
            '%s = (%s = %s)',
            $hash1 === $hash2 ? 'true' : 'false',
            $hash1,
            $hash2
        );

        if (\function_exists('spl_object_id')) {
            $id1 = \spl_object_id($object1);
            $id2 = \spl_object_id($object2);

            $result .= \sprintf(
                '; %s = (%s = %s)',
                $id1 === $id2 ? 'true' : 'false',
                $id1,
                $id2
            );
        }

        return $result;
    }
}
