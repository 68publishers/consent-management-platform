<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\Ui\Presenter;
use Nette\Application\Request;
use Nette\Application\BadRequestException;

final class Error4xxPresenter extends Presenter
{
	/**
	 * {@inheritDoc}
	 *
	 * @throws \Nette\Application\BadRequestException
	 */
	public function startup(): void
	{
		parent::startup();

		if (NULL !== $this->getRequest() && !$this->getRequest()->isMethod(Request::FORWARD)) {
			$this->error();
		}
	}

	/**
	 * @param \Nette\Application\BadRequestException $exception
	 *
	 * @return void
	 */
	public function renderDefault(BadRequestException $exception): void
	{
		// load template 403.latte or 404.latte or ... 4xx.latte
		$file = __DIR__ . "/templates/Error.{$exception->getCode()}.latte";
		$file = is_file($file) ? $file : __DIR__ . '/templates/Error.4xx.latte';

		$template = $this->getTemplate();

		$template->setFile($file);
	}
}
