<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use DICIT\Registry;

$registry = new Registry();

$a = new \stdClass();
$a->property = 'value';

$registry->set('a', $a);

var_dump($registry->get('a'));

$a = new \stdClass();
$a->overriddenProperty = 'overridenValue';

var_dump($registry->get('a'));