<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\LightVarDumperProviders;

use Vipkwd\VarDumper\Objects\HasherFactory;

/**
 * @internal
 */
final class ProviderDynamicDump implements \IteratorAggregate
{
    public function getIterator()
    {
        return new \ArrayIterator(array(
            'visibilityModifiers' => $this->getVisibilityModifiers(),
        ));
    }

    private function getVisibilityModifiers()
    {
        $hasher = HasherFactory::create();

        $object = new TestObject();
        $object->setPrivate('private variable');
        $object->setProtected('protected variable');
        $object->public = 'public variable';
        $object->dynamicPublic = 'another public variable';

        $objectDump
            = <<<OBJECT
object(Vipkwd\VarDumper\LightVarDumperProviders\TestObject) #{$hasher->getHashId($object)} (5) {
    public static \$static =>             “static value”
    public \$public =>                    “public variable”
    protected \$protected =>              “protected variable”
    protected static \$protectedStatic => “protected static value”
    \$dynamicPublic =>                    “another public variable”
}

OBJECT;

        return array($object, \explode("\n", $objectDump));
    }
}
