<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new HttpCache(new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']));
    //return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
