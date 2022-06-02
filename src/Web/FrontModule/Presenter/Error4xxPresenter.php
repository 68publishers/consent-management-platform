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

		$this->setLayout(FALSE);
	}

	/**
	 * @param \Nette\Application\BadRequestException $exception
	 *
	 * @return void
	 */
	public function renderDefault(BadRequestException $exception): void
	{
		$errorCode = $exception->getCode();
		$errorCodeString = in_array($errorCode, [404, 403], TRUE) ? (string) $errorCode : '4xx';

		$this->template->errorCode = $errorCode;
		$this->template->errorCodeString = $errorCodeString;
	}
}
