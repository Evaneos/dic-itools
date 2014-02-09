<?php

namespace DICIT\Tools\Validation;

use DICIT\ArrayResolver;
class EmptyNodeValidator implements ConfigValidator
{

    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode)
    {
        $array = $serviceNode->extract();

        if (empty($array)) {
            $validator->addError('Service configuration is empty.');
        }
    }
}