<?php

declare(strict_types=1);

namespace App\Application\Fixture;

final class DemoFixtures extends AbstractFixture
{
	/**
	 * {@inheritDoc}
	 */
	protected function loadFixtures(): array
	{
		return require __DIR__ . '/resources/demo.php';
	}
}
