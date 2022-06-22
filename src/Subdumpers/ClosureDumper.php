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
use Vipkwd\VarDumper\Properties\PropertyInterface;
use Vipkwd\VarDumper\Properties\VarProperty;
use Vipkwd\VarDumper\Strings\LinePart;
use Vipkwd\VarDumper\Strings\Parts;

/**
 * @internal
 */
final class ClosureDumper extends AbstractObjectDumper
{
    public function supports($var)
    {
        return \is_object($var) && $var instanceof \Closure;
    }

    public function dump($closure)
    {
        $header = new LinePart(\sprintf(
            'object(%s) #%s {[',
            $this->getClassName($closure),
            $this->container->getHasher()->getHashId($closure)
        ));

        $result = new Parts();
        $result->appendPart($header);

        $properties = array();
        foreach ($this->decorateProperties($this->getProperties($closure)) as $property) {
            $properties[$property->getName()] = $property->getValue();
        }
        $body = ArrayBigDumper::dumpBody($properties, $this->container);
        $body->addIndent($this->container->getConfig()->getIndent());
        $result->appendPart($body);

        $result->appendPart(new LinePart(']}'));

        return $result;
    }

    /**
     * @param PropertyInterface[] $properties
     *
     * @return PropertyInterface[]
     */
    private function decorateProperties(array $properties)
    {
        $result = array();

        foreach ($properties as $property) {
            if ('filename' === $property->getName()) {
                $property = new VarProperty(
                    $property->getName(),
                    FileNameDecorator::decorateFileName($property->getValue(), $this->container->getConfig()->getMaxFileNameDepth()),
                    $property->isPublic() ? VarProperty::VISIBILITY_PUBLIC : ($property->isProtected() ? VarProperty::VISIBILITY_PROTECTED : VarProperty::VISIBILITY_PRIVATE),
                    $property->getDeclaringClass(),
                    $property->isStatic(),
                    $property->isVirtual()
                );
            }

            $result[] = $property;
        }

        return $result;
    }
}
