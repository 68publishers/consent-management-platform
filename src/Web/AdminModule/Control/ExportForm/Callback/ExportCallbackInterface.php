<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm\Callback;

use App\Application\DataProcessor\DataProcessFactory;

interface ExportCallbackInterface
{
	/**
	 * @return string
	 */
	public function name(): string;

	/**
	 * @param \App\Application\DataProcessor\DataProcessFactory $dataProcessFactory
	 * @param string                                            $format
	 * @param array                                             $options
	 *
	 * @return string
	 */
	public function __invoke(DataProcessFactory $dataProcessFactory, string $format, array $options): string;
}
