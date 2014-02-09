<?php

namespace DICIT\Tools\Validation;

use DICIT\ArrayResolver;

interface ConfigValidator
{

    public function validateService(Validator $validator, ArrayResolver $global, $serviceName, ArrayResolver $serviceNode);
}
