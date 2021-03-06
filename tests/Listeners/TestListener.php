<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper\Listeners;

use Vipkwd\VarDumper\SyntaxTest;
use Vipkwd\VarDumper\TestEnv;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @internal
 */
final class TestListener implements \PHPUnit_Framework_TestListener
{
    private static $times = array();

    public static function flush()
    {
        $times = self::$times;
        self::$times = array();

        if (empty($times)) {
            return;
        }

        \usort($times, function ($left, $right) {
            return $left[0] == $right[0]
                ? 0
                : ($left[0] < $right[0] ? 1 : -1);
        });

        $wholeTime = 0;
        \array_walk($times, function ($element) use (&$wholeTime) {
            $wholeTime += $element[0];
        });

        $cpTimes = \array_slice($times, 0, 10);

        $maxLength = 0;
        \array_walk($cpTimes, function ($element) use (&$maxLength) {
            $len = \mb_strlen($element[1]);
            if ($len > $maxLength) {
                $maxLength = $len;
            }
        });

        $output = new ConsoleOutput();
        $header = '<bg=yellow;fg=black>ms         %        ' . \str_pad('name', $maxLength, ' ') . '</>';
        $output->writeln($header);
        foreach ($cpTimes as $timeData) {
            list($time, $name) = $timeData;
            $output->writeln(\sprintf(
                '<bg=yellow;fg=black>% 7.2f    % 5.2f    %s</>',
                $time * 1000,
                $time / $wholeTime * 100,
                \str_pad($name, $maxLength, ' ')
            ));
        }
    }

    public function __construct()
    {
        $output = new ConsoleOutput();
        $output->writeln(\sprintf('PHP %s', \phpversion()));
        if (TestEnv::isSpeedTest()) {
            \register_shutdown_function(function () {
                TestListener::flush();
            });
            SyntaxTest::requireWholeSrc();
        }
    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        $name = $test instanceof \PHPUnit_Framework_TestCase
            ? \get_class($test) . '::' . $test->getName()
            : \get_class($test);

        self::$times[] = array($time, $name);
    }

    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
    }

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
    }
}
