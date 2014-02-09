<?php

namespace DICIT\Tools\Validation;

use DICIT\ArrayResolver;
class CyclicDependencyValidator implements ConfigValidator
{

    private $currentNodeName;

    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode)
    {
        $this->currentNodeName = $serviceName;

        foreach ($serviceNode->resolve('props', array()) as $name) {
            $name = substr($name, 1);
            $dependencyNode = $global->resolve('classes.' . $name, array());

            if ($this->dependsOnCurrentNode($name, $dependencyNode)) {
                if ($this->hasOneSingleton($serviceNode, $name, $dependencyNode)) {
                    $validator->addWarning('Cyclic dependency detected with ' . $name);
                }
                else {
                    $validator->addError('Unsatisfiable cyclic dependency detected with ' . $name);
                }
            }
        }
    }

    private function dependsOnCurrentNode($name, ArrayResolver $config)
    {
        foreach ($config->resolve('props', array()) as $dependency) {
            if (substr($dependency, 0, 1) == '@' && substr($dependency, 1) == $this->currentNodeName) {
                return true;
            }
        }

        return false;
    }

    private function hasOneSingleton(ArrayResolver $serviceNode, $dependencyName, ArrayResolver $dependencyNode)
    {
        if ($serviceNode->resolve('singleton', false) == true) {
            return true;
        }

        return $dependencyNode->resolve('singleton', false) == true;
    }
}