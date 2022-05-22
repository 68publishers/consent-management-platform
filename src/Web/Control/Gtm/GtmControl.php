<?php

declare(strict_types=1);

namespace App\Web\Control\Gtm;

use App\Web\Ui\Control;

final class GtmControl extends Control
{
	private ?string $containerId;

	/**
	 * @param string|NULL $containerId
	 */
	public function __construct(?string $containerId)
	{
		$this->containerId = $containerId;
	}

	/**
	 * @return void
	 */
	public function renderScript(): void
	{
		$this->doRender('script');
	}

	/**
	 * @return void
	 */
	public function renderNoscript(): void
	{
		$this->doRender('noscript');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->containerId = $this->containerId;
	}
}
