<?php

namespace DICIT\Tools\Validation;

use DICIT\ArrayResolver;

class ConstructorArgumentsValidator implements ConfigValidator
{

    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode)
    {
        $class = $serviceNode->resolve('class');

        if ($class !== null) {
            if (! class_exists($class) && ! interface_exists($class)) {
                $validator->addError('Class not found : ' . $class);
            }
        }
    }

}