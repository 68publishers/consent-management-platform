<?php

declare(strict_types=1);

namespace App\Web\Ui\Form;

trait FormFactoryOptionsTrait
{
	private array $formFactoryOptions = [];

	/**
	 * @param array $options
	 *
	 * @return void
	 */
	public function setFormFactoryOptions(array $options): void
	{
		$this->formFactoryOptions = $options;
	}

	/**
	 * @return array
	 */
	protected function getFormFactoryOptions(): array
	{
		return $this->formFactoryOptions;
	}
}
