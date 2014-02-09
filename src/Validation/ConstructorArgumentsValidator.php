<?php
namespace DICIT\Tools\Validation;

use DICIT\ArrayResolver;

class ConstructorArgumentsValidator implements ConfigValidator
{

    public function validateService(Validator $validator, ArrayResolver $global, $serviceName,
        ArrayResolver $serviceNode)
    {
        $class = $serviceNode->resolve('class');

        if ($class !== null) {
            if (! class_exists($class) && ! interface_exists($class)) {
                $validator->addError('Class not found : ' . $class);
                return;
            }

            $reflectionClass = new \ReflectionClass($class);

            /* @var $reflectionCtor \ReflectionMethod */
            $reflectionCtor = $reflectionClass->getConstructor();
            $ctorArgs = $serviceNode->resolve('args', array());

            if ($reflectionCtor == null) {
                if (! empty($args)) {
                    $validator->addWarning('Constructor arguments are provided, but no constructor was found.');
                }

                return;
            }

            if (count($ctorArgs) < $reflectionCtor->getNumberOfRequiredParameters()) {
                $validator->addError(
                    sprintf('Invalid parameter count : %d instead of %d required.', count($ctorArgs),
                        $reflectionCtor->getNumberOfParameters()));
            }
        }
    }
}
