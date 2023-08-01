<?php

declare(strict_types=1);

use Apitte\Core\Application\IApplication as ApiApplication;
use App\Bootstrap;
use Nette\Application\Application as WebApplication;

require __DIR__ . '/../vendor/autoload.php';

$container = Bootstrap::boot()
    ->createContainer()
    ->getByType(str_starts_with($_SERVER['REQUEST_URI'], '/api/') ? ApiApplication::class : WebApplication::class)
    ->run();
