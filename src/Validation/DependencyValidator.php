<?php
namespace DICIT\Tools\Validation;

use DICIT\ArrayResolver;

class DependencyValidator implements ConfigValidator
{

    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode)
    {
        $this->validateNode($validator, $global, $serviceNode->resolve('arguments', array()));

        $className = $serviceNode->resolve('class');
        $properties = $serviceNode->resolve('props', array());
        $this->validateNode($validator, $global, $properties);

        if ($className == '\stdClass' || ! class_exists($className)) {
            return;
        }

        $reflectionClass = new \ReflectionClass($className);
        foreach ($properties as $propertyName => $propertyValue) {
            if (property_exists($className, $propertyName)) {
                continue;
            }

            if (method_exists($className, '__set')) {
                $validator->addWarning(
                    sprintf("Undefined target property'%s', but a magic set method was found.",
                        $propertyName));
            }
            else {
                $validator->addError(
                    sprintf("Undefined target property'%s'.",
                        $propertyName));
            }
        }
    }

    private function validateNode(Validator $validator, ArrayResolver $global, ArrayResolver $node)
    {
        foreach ($node as $arg) {
            $prefix = substr($arg, 0, 1);
            $partial = substr($arg, 1);

            if ($prefix == '@') {
                $dependencyNode = $global->resolve('classes.' . $partial, null);
                if ($dependencyNode === null) {
                    $validator->addError('Missing dependency : ' . $partial);
                }
            }
            elseif ($prefix == '%') {
                $parameterNode = $global->resolve('parameters.' . $partial, null);
                if ($parameterNode === null) {
                    $validator->addError('Missing parameter definition : ' . $partial);
                }
            }
        }
    }
}
