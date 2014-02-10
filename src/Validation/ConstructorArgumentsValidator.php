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
            $ctorArgs = $serviceNode->resolve('arguments', array());

            if ($reflectionCtor == null) {
                if (! empty($ctorArgs)) {
                    $validator->addWarning('Constructor arguments are provided, but no constructor was found.');
                }

                return;
            }

            if (count($ctorArgs) < $reflectionCtor->getNumberOfRequiredParameters()) {
                $validator->addError(
                    sprintf('Invalid parameter count : %d instead of %d required.', count($ctorArgs),
                        $reflectionCtor->getNumberOfRequiredParameters()));
            }
            elseif (count($ctorArgs) > $reflectionCtor->getNumberOfParameters()) {
                $validator->addWarning(
                    sprintf('Invalid parameter count : %d instead of %d defined.', count($ctorArgs),
                        $reflectionCtor->getNumberOfParameters()));
            }

            /* @var $parameter \ReflectionParameter */
            $i = 0;
            $args = $ctorArgs->extract();
            foreach ($reflectionCtor->getParameters() as $parameter) {
                if ($i >= count($args)) {
                    break;
                }

                $hint = $this->getHint($reflectionCtor->getDocComment(), $parameter->getName());
                $type = $this->resolveType($global, $args[$i]);

                if ($type != 'mixed' && $type != $hint && $hint != array_pop(explode('\\', $type))) {
                    $validator->addWarning(
                        sprintf('Parameter type mismatch : %s instead of %s defined.', $type,
                            $hint));
                }

                $i++;
            }
        }
    }

    private function resolveType(ArrayResolver $global, $reference)
    {
        $prefix = substr($reference, 0, 1);
        if ($prefix == '@') {
            return $global->resolve('classes.' . substr($reference, 1) . '.class');
        }
        else {
            return 'mixed';
        }
    }

    // Regex
    private function getHint($docComment, $varName)
    {
        $matches = array();
        $count = preg_match_all('/@param[\t\s]*(?P<type>[^\t\s]*)[\t\s]*\$(?P<name>[^\t\s]*)/sim', $docComment,
            $matches);
        if ($count > 0) {
            foreach ($matches['name'] as $n => $name) {
                if ($name == $varName) {
                    return $matches['type'][$n];
                }
            }
        }
        return null;
    }
}
