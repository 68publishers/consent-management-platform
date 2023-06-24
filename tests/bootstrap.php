<?php

declare(strict_types=1);

use Tester\Environment;

$loader = @include __DIR__ . '/../vendor/autoload.php';

if (!$loader) {
    echo 'Install Nette Tester using `composer install`';
    exit(1);
}

Environment::setup();

return $loader;
