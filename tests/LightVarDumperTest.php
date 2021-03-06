<?php

/*
 * This file is part of the vipkwd/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me> | Vipkkwd <service@vipkwd.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipkwd\VarDumper;

use Vipkwd\VarDumper\Helpers\Container;
use Vipkwd\VarDumper\Helpers\IntValue;
use Vipkwd\VarDumper\Helpers\Strings;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderDump;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderDumpConstants;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderDynamicDump;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderExceptions;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderIndent;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderMaxChildren;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderMaxDepth;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderMaxStringLength;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderMultiLine;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderPlaceInCode;
use Vipkwd\VarDumper\LightVarDumperProviders\ProviderRecursive;
use Vipkwd\VarDumper\Subdumpers\NativeDumper;
use Vipkwd\VarDumper\Subdumpers\SubdumpersCollection;

/**
 * @internal
 */
final class LightVarDumperTest extends BaseTestCase
{
    private $wasDumperReset = false;

    /**
     * @dataProvider providerDump
     *
     * @param mixed  $var
     * @param string $expectedDump
     */
    public function testDump($var, $expectedDump)
    {
        if (!$this->wasDumperReset) {
            $this->reinitAllDumpers();
        }

        $dumper = new LightVarDumper();
        if ('#' === $expectedDump[0]) {
            $this->assertRegExp($expectedDump, $dumper->dumpAsString($var));
        } else {
            $this->assertSame($expectedDump, $dumper->dumpAsString($var), $expectedDump . "==========\n" . $dumper->dumpAsString($var));
        }

        $this->assertZeroDepth($dumper);
        $this->assertEmptyReferences($dumper);
    }

    public function providerDump()
    {
        return \array_merge(
            \iterator_to_array(new ProviderDump()),
            \iterator_to_array(new ProviderDumpConstants())
        );
    }

    /**
     * @dataProvider providerThrowable
     *
     * @param VarDumperInterface    $dumper
     * @param \Exception|\Throwable $exception
     * @param string                $expectedDump
     */
    public function testThrowable(VarDumperInterface $dumper, $exception, $expectedDump)
    {
        /*
         * There are small differences between HHVM and PHP, e.g. name of function for closure:
         *
         * PHP:   Vipkwd\VarDumper\LightVarDumperProviders\ProviderExceptions->Vipkwd\VarDumper\LightVarDumperProviders\{closure}()
         * HHVM: {closure}()
         *
         * https://travis-ci.org/vipkwd/var-dumper/jobs/612736181
         */
        if (\defined('HHVM_VERSION')) {
            $this->assertInternalType('string', $dumper->dumpAsString($exception));

            return;
        }

        /*
         * PHP ^5.4:  Vipkwd\VarDumper\LightVarDumperProviders\ProviderExceptions->Vipkwd\VarDumper\LightVarDumperProviders\{closure}()
         * PHP 5.3.*: Vipkwd\VarDumper\LightVarDumperProviders\{closure}()
         *
         * https://travis-ci.org/vipkwd/var-dumper/jobs/612762242
         */
        if (\version_compare(\PHP_VERSION, '5.4') < 0) {
            $regex = '/[a-zA-Z0-9_]+' . \preg_quote('->', '/') . '[a-zA-Z0-9_\\\\]+' . \preg_quote('{closure}()', '/') . '/';
            $expectedDump = \preg_replace($regex, '{closure}()', $expectedDump);

            $this->assertSame($expectedDump, $dumper->dumpAsString($exception));

            return;
        }

        $this->assertSame($expectedDump, $dumper->dumpAsString($exception));
    }

    public function providerThrowable()
    {
        return \iterator_to_array(new ProviderExceptions());
    }

    /**
     * HHVM changes order of properties.
     *
     * @dataProvider providerDynamicDump
     *
     * @param          $var
     * @param string[] $lines
     *
     * @see https://travis-ci.org/vipkwd/var-dumper/jobs/462117181
     */
    public function testDynamicDump($var, array $lines)
    {
        if (!$this->wasDumperReset) {
            $this->reinitAllDumpers();
        }

        $dumper = new LightVarDumper();
        $dump = $dumper->dumpAsString($var);
        $dumpLines = \explode("\n", $dump);

        $this->assertNotEmpty($lines);
        foreach ($lines as $line) {
            $this->assertContains($line, $dumpLines);
        }
        $this->assertZeroDepth($dumper);
        $this->assertEmptyReferences($dumper);
    }

    public function providerDynamicDump()
    {
        return \iterator_to_array(new ProviderDynamicDump());
    }

    public function testNativeDumper()
    {
        $dumper = new LightVarDumper();

        $subdumper = $this->getPrivateProperty($dumper, 'subdumper');
        $refSubDumper = new \ReflectionProperty($subdumper, 'subdumpers');
        $refSubDumper->setAccessible(true);
        $refSubDumper->setValue($subdumper, array(new NativeDumper()));

        $dump = $dumper->dumpAsString(5);
        $this->assertInternalType('string', $dump);
        $this->assertNotSame('', $dump);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage None of the subdumpers supports this variable [NULL]
     */
    public function testNoSupportedSubdumper()
    {
        $dumper = new LightVarDumper();

        $subdumper = $this->getPrivateProperty($dumper, 'subdumper');
        $refSubDumper = new \ReflectionProperty($subdumper, 'subdumpers');
        $refSubDumper->setAccessible(true);
        $refSubDumper->setValue($subdumper, array());

        $dumper->dump(null);
    }

    /**
     * @dataProvider providerPlaceInCode
     *
     * @param LightVarDumper $dumper
     * @param                $var
     * @param                $dump
     */
    public function testPlaceInCode(LightVarDumper $dumper, $var, $dump)
    {
        $this->assertSame($dump, $dumper->dumpAsString($var));
        $this->assertZeroDepth($dumper);
        $this->assertEmptyReferences($dumper);
    }

    public function providerPlaceInCode()
    {
        return \iterator_to_array(new ProviderPlaceInCode());
    }

    /**
     * @dataProvider providerMaxDepth
     *
     * @param int    $limit
     * @param        $var
     * @param string $dump
     */
    public function testMaxDepth($limit, $var, $dump)
    {
        $dumper = new LightVarDumper();
        $dumper2 = $dumper->setMaxDepth($limit);
        $this->assertSame($dumper2, $dumper);
        $this->assertSame($dump, $dumper->dumpAsString($var));
    }

    public function providerMaxDepth()
    {
        return \iterator_to_array(new ProviderMaxDepth());
    }

    /**
     * @dataProvider providerMaxStringLength
     *
     * @param int    $limit
     * @param string $string
     * @param string $dump
     */
    public function testMaxStringLength($limit, $string, $dump)
    {
        $dumper = new LightVarDumper();
        $dumper2 = $dumper->setMaxStringLength($limit);
        $this->assertSame($dumper2, $dumper);
        $this->assertSame($dump, $dumper->dumpAsString($string));
    }

    public function providerMaxStringLength()
    {
        return \iterator_to_array(new ProviderMaxStringLength());
    }

    /**
     * @dataProvider providerMaxChildren
     *
     * @param int    $limit
     * @param        $iterable
     * @param string $dump
     */
    public function testMaxChildren($limit, $iterable, $dump)
    {
        $dumper = new LightVarDumper();
        $dumper2 = $dumper->setMaxChildren($limit);
        $this->assertSame($dumper2, $dumper);
        $this->assertSame($dump, $dumper->dumpAsString($iterable));
    }

    public function providerMaxChildren()
    {
        return \iterator_to_array(new ProviderMaxChildren());
    }

    /**
     * @dataProvider providerIndent
     *
     * @param string $indent
     * @param        $var
     * @param string $dump
     */
    public function testIndent($indent, $var, $dump)
    {
        $dumper = new LightVarDumper();
        $dumper2 = $dumper->setIndent($indent);
        $this->assertSame($dumper2, $dumper);
        $this->assertSame($dump, $dumper->dumpAsString($var));
    }

    public function providerIndent()
    {
        return \iterator_to_array(new ProviderIndent());
    }

    /**
     * @dataProvider providerRecursive
     *
     * @param             $var
     * @param bool|string $expectedDump
     */
    public function testRecursive($var, $expectedDump)
    {
        $dumper = new LightVarDumper();
        $dump = $dumper->dumpAsString($var);
        $this->assertInternalType('string', $dump);
        if (false !== $expectedDump) {
            $this->assertSame($expectedDump, $dumper->dumpAsString($var));
        }
    }

    public function providerRecursive()
    {
        return \iterator_to_array(new ProviderRecursive());
    }

    /**
     * @dataProvider providerMultiLine
     *
     * @param int    $stringLimit
     * @param int    $lineLimit
     * @param string $input
     * @param string $expected
     */
    public function testMultiLine($stringLimit, $lineLimit, $input, $expected)
    {
        $dumper = new LightVarDumper();
        $dumper
            ->setMaxStringLength($stringLimit)
            ->setMaxLineLength($lineLimit);
        $dump = $dumper->dumpAsString($input);
        $this->assertInternalType('string', $dump);
        $this->assertSame($expected, $dump);
    }

    public function providerMultiLine()
    {
        return \iterator_to_array(new ProviderMultiLine());
    }

    /**
     * @dataProvider providerInvalidMaxDepth
     *
     * @expectedException \InvalidArgumentException
     *
     * @param $limit
     */
    public function testInvalidMaxDepth($limit)
    {
        $dumper = new LightVarDumper();
        $dumper->setMaxDepth($limit);
    }

    public function providerInvalidMaxDepth()
    {
        return array(
            array(0.1),
            array(0),
            array(-1),
            array('-1'),
            array(false),
        );
    }

    /**
     * @dataProvider providerInvalidMaxChildren
     *
     * @expectedException \InvalidArgumentException
     *
     * @param $limit
     */
    public function testInvalidMaxChildrenh($limit)
    {
        $dumper = new LightVarDumper();
        $dumper->setMaxChildren($limit);
    }

    public function providerInvalidMaxChildren()
    {
        return $this->providerInvalidMaxDepth();
    }

    /**
     * @dataProvider providerInvalidMaxStringLength
     *
     * @expectedException \InvalidArgumentException
     *
     * @param $limit
     */
    public function testInvalidMaxStringLength($limit)
    {
        $dumper = new LightVarDumper();
        $dumper->setMaxStringLength($limit);
    }

    public function providerInvalidMaxStringLength()
    {
        return array(
            array(0.1),
            array(0),
            array(-1),
            array('-1'),
            array(false),
            array(1),
        );
    }

    /**
     * @dataProvider providerInvalidMaxLineLength
     *
     * @expectedException \InvalidArgumentException
     *
     * @param $limit
     */
    public function testInvalidMaxLineLength($limit)
    {
        $dumper = new LightVarDumper();
        $dumper->setMaxLineLength($limit);
    }

    public function providerInvalidMaxLineLength()
    {
        return $this->providerInvalidMaxStringLength();
    }

    /**
     * @dataProvider providerInvalidIndent
     *
     * @expectedException \InvalidArgumentException
     *
     * @param $indent
     */
    public function testInvalidIndent($indent)
    {
        $dumper = new LightVarDumper();
        $dumper->setIndent($indent);
    }

    public function providerInvalidIndent()
    {
        $result = array();
        foreach (\array_keys(Strings::$replaceChars) as $whiteChar) {
            $result[] = array($whiteChar);
        }

        $result[] = array('');

        return $result;
    }

    public function testSetMaxFileNameDepth()
    {
        if (\DIRECTORY_SEPARATOR !== '/') {
            $this->markTestSkipped('Does not work on Windows');
        }
        $dumper = new LightVarDumper(true);
        $dumper->setMaxFileNameDepth(1);

        if (\defined('HHVM_VERSION')) {
            $this->assertRegExp(
                '/' . \preg_quote('(...)/LightVarDumperTest.php:', '/') . '[0-9]+' . \preg_quote(":\ntrue\n", '/') . '/',
                $dumper->dumpAsString(true)
            );

            return;
        }

        $this->assertSame(
            \sprintf("(...)/LightVarDumperTest.php:%d:\ntrue\n", __LINE__ + 1),
            $dumper->dumpAsString(true)
        );
    }

    private function reinitAllDumpers()
    {
        $classes = array(
            'Vipkwd\VarDumper\Subdumpers\ArrayRecursiveDumper',
            'Vipkwd\VarDumper\Subdumpers\ScalarDumper',
            'Vipkwd\VarDumper\Subdumpers\StringDumper',
        );
        foreach ($classes as $class) {
            $reflectionInit = new \ReflectionProperty($class, 'inited');
            $reflectionInit->setAccessible(true);
            $reflectionInit->setValue(false);
            $this->wasDumperReset = true;
        }
    }

    private function assertZeroDepth(LightVarDumper $dumper)
    {
        /** @var IntValue $depth */
        $depth = $this->getPrivateProperty($this->getContainer($dumper), 'depth');

        $this->assertSame(0, $depth->getValue());
    }

    private function assertEmptyReferences(LightVarDumper $dumper)
    {
        $references = $this->getContainer($dumper)->getReferences();
        $array = $this->getPrivateProperty($references, 'array');
        $this->assertSame(0, \count($array));
    }

    /**
     * @param LightVarDumper $dumper
     *
     * @return Container
     */
    private function getContainer(LightVarDumper $dumper)
    {
        /** @var SubdumpersCollection $subdumper */
        $subdumper = $this->getPrivateProperty($dumper, 'subdumper');

        return $this->getPrivateProperty($subdumper, 'container');
    }

    private function getPrivateProperty($object, $name)
    {
        $property = new \ReflectionProperty(\get_class($object), $name);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
