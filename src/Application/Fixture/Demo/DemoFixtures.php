<?php

declare(strict_types=1);

namespace App\Application\Fixture\Demo;

use Doctrine\Persistence\ObjectManager;
use App\Application\Fixture\AbstractFixture;

final class DemoFixtures extends AbstractFixture
{
	/**
	 * {@inheritDoc}
	 */
	protected function loadFixtures(ObjectManager $manager): array
	{
		return require __DIR__ . '/../resources/demo.php';
	}
}
