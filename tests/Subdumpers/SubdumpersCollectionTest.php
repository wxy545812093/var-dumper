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

use Vipkwd\VarDumper\BaseTestCase;
use Vipkwd\VarDumper\Config\AbstractConfig;
use Vipkwd\VarDumper\Helpers\Container;
use Vipkwd\VarDumper\Strings\LinePart;

/**
 * @internal
 */
final class SubdumpersCollectionTest extends BaseTestCase
{
    public function testNotSupported()
    {
        $this->setExpectedExceptionRegExp(
            'RuntimeException',
            '/^None of the subdumpers supports this variable \[.*\]$/'
        );

        $collection = $this->createCollection(null, array());
        $collection->dumpAsPart(true);
    }

    public function testOrder()
    {
        $firstDumper = $this->createSubdumperMock();
        $firstDumper
            ->expects($this->once())
            ->method('supports')
            ->willReturn(false);
        $firstDumper
            ->expects($this->never())
            ->method('dump');

        $secondDumper = $this->createSubdumperMock();
        $secondDumper
            ->expects($this->once())
            ->method('supports')
            ->willReturn(true);
        $secondDumper
            ->expects($this->once())
            ->method('dump')
            ->willReturnCallback(function () {
                throw new VarNotSupportedException();
            });

        $thirdDumper = $this->createSubdumperMock();
        $thirdDumper
            ->expects($this->once())
            ->method('supports')
            ->willReturn(true);
        $thirdDumper
            ->expects($this->once())
            ->method('dump')
            ->willReturn(new LinePart(''));

        $fourthDumper = $this->createSubdumperMock();
        $fourthDumper
            ->expects($this->never())
            ->method('supports');
        $fourthDumper
            ->expects($this->never())
            ->method('dump');

        $collection = $this->createCollection(
            null,
            array($firstDumper, $secondDumper, $thirdDumper, $fourthDumper)
        );
        $collection->dumpAsPart(true);

        $reflection = new \ReflectionProperty($collection, 'container');
        $reflection->setAccessible(true);
        /** @var Container $container */
        $container = $reflection->getValue($collection);
        $this->assertSame(0, $container->getDepth()->getValue());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SubdumperInterface
     */
    private function createSubdumperMock()
    {
        return $this->getMockBuilder('Vipkwd\VarDumper\Subdumpers\SubdumperInterface')->getMock();
    }

    /**
     * @param null|AbstractConfig $config
     * @param null|array          $subdumpers
     *
     * @return SubdumpersCollection
     */
    private function createCollection(AbstractConfig $config = null, array $subdumpers = null)
    {
        return new SubdumpersCollection($config ?: $this->createConfigMock(), $subdumpers);
    }

    /**
     * @return AbstractConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createConfigMock()
    {
        return $this->getMockBuilder('Vipkwd\VarDumper\Config\AbstractConfig')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
