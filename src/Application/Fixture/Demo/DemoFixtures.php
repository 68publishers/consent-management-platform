<?php

declare(strict_types=1);

namespace App\Application\Fixture\Demo;

use App\Application\Fixture\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class DemoFixtures extends AbstractFixture
{
    protected function loadFixtures(ObjectManager $manager): array
    {
        return require __DIR__ . '/../resources/demo.php';
    }
}
