<?php

declare(strict_types=1);

use App\Bootstrap;
use Nette\Application\Application as WebApplication;
use Apitte\Core\Application\IApplication as ApiApplication;

require __DIR__ . '/../vendor/autoload.php';

$container = Bootstrap::boot()
	->createContainer()
	->getByType(0 === strpos($_SERVER['REQUEST_URI'], '/api/') ? ApiApplication::class : WebApplication::class)
	->run();
