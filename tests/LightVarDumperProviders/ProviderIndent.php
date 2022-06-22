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

/**
 * @internal
 */
final class ProviderIndent implements \IteratorAggregate
{
    public function getIterator()
    {
        $result = array(
            'dashes' => $this->getDashes(),
            'dots' => $this->getDots(),
        );

        return new \ArrayIterator($result);
    }

    private function getDashes()
    {
        $var = array(array(array()));
        $dump
            = <<<'DUMP'
array(1) {
----[0] => array(1) {array(0) {}}
}

DUMP;

        return array('----', $var, $dump);
    }

    private function getDots()
    {
        $var = array(array(array(1, 2, 3)));
        $dump
            = <<<'DUMP'
array(1) {
..[0] =>
....array(1) {
......[0] => array(3) {1, 2, 3}
....}
}

DUMP;

        return array('..', $var, $dump);
    }
}
