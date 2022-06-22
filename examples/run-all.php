<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

foreach (\glob(__DIR__ . \DIRECTORY_SEPARATOR . '*.php') as $file) {
    $basename = \basename($file);
    if (\in_array($basename, array('run-all.php', 'exception-variadic.php', 'init.php'), true)) {
        continue;
    }

    echo '##### ', $basename, "\n", \shell_exec('php ' . \escapeshellarg($file)), "\n";
}
