<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (\version_compare(\PHP_VERSION, '5.6') >= 0) {
    require_once __DIR__ . \DIRECTORY_SEPARATOR . 'exception-variadic.php';
} else {
    echo "PHP >= 5.6 required\n";
}
