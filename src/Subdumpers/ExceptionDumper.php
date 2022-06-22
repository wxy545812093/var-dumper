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

use Vipkwd\VarDumper\Helpers\FileNameDecorator;
use Vipkwd\VarDumper\Strings\LinePart;
use Vipkwd\VarDumper\Strings\PartInterface;
use Vipkwd\VarDumper\Strings\Parts;
use Vipkwd\VarDumper\Subdumpers\Helpers\StackTraceHelper;

/**
 * @internal
 */
final class ExceptionDumper extends AbstractObjectDumper
{
    public function supports($var)
    {
        $baseClass = \version_compare(\PHP_VERSION, '7.0') >= 0 ? 'Throwable' : 'Exception';

        return \is_object($var) && $var instanceof $baseClass;
    }

    /**
     * @param \Exception|\Throwable| $throwable
     *
     * @return PartInterface
     */
    public function dump($throwable)
    {
        $result = new Parts();
        $header = new LinePart(\sprintf(
            'object(%s) #%s {[',
            $this->getClassName($throwable),
            $this->container->getHasher()->getHashId($throwable)
        ));
        $result->appendPart($header);

        $array = array(
            'message' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
            'file' => FileNameDecorator::decorateFileName($throwable->getFile(), $this->container->getConfig()->getMaxFileNameDepth()) . ':' . $throwable->getLine(),
            'previous' => $throwable->getPrevious(),
        );
        $body = ArrayBigDumper::dumpBody($array, $this->container);
        $body->addIndent($this->container->getConfig()->getIndent());
        $result->appendPart($body);

        $childDiff = $this->container->getConfig()->getMaxChildren() - \count($array);

        if ($childDiff > 0) {
            $trace = $this->prepareTrace($throwable->getTrace());
            $trace->addIndent($this->container->getConfig()->getIndent());
            $result->appendPart($trace);
        } elseif (0 === $childDiff) {
            $dotsPart = new LinePart('(...)');
            $dotsPart->addIndent($this->container->getConfig()->getIndent());
            $result->appendPart($dotsPart);
        }

        $result->appendPart(new LinePart(']}'));

        return $result;
    }

    /**
     * @param array $trace
     *
     * @return PartInterface
     */
    private function prepareTrace(array $trace)
    {
        // @codeCoverageIgnoreStart
        if (!$trace) {
            return new LinePart('[trace] =>    []');
        }
        // @codeCoverageIgnoreEnd

        if ($this->container->getDepth()->getValue() === $this->container->getConfig()->getMaxDepth()) {
            return new LinePart('[trace] =>    [...]');
        }

        $result = new Parts();
        $result->appendPart(new LinePart('[trace] =>'));

        $this->container->getDepth()->incr();
        $trace = StackTraceHelper::dumpStackTraceAsPart($trace, $this->container);
        $this->container->getDepth()->decr();
        $trace->addIndent($this->container->getConfig()->getIndent());

        $result->appendPart($trace);

        return $result;
    }
}
