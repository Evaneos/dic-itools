<?php
namespace DICIT\Tools\Validation;

use DICIT\ArrayResolver;

class DependencyValidator implements ConfigValidator
{

    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode)
    {
        $this->validateNode($validator, $global, $serviceNode->resolve('args', array()));
        $this->validateNode($validator, $global, $serviceNode->resolve('props', array()));
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