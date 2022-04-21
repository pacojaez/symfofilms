<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

//Ignorar petiiciones OPTIONS
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
    die();

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
