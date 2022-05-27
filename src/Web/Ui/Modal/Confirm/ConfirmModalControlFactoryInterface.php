<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Confirm;

interface ConfirmModalControlFactoryInterface
{
	/**
	 * @param \Nette\Utils\Html|string $title
	 * @param \Nette\Utils\Html|string $question
	 * @param callable                 $callback
	 * @param array                    $args
	 *
	 * @return \App\Web\Ui\Modal\Confirm\ConfirmModalControl
	 */
	public function create($title, $question, callable $callback, array $args = []): ConfirmModalControl;
}
