<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\Ui\Presenter;
use SixtyEightPublishers\SmartNetteComponent\Annotation\LoggedOut;
use SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface;

/**
 * @LoggedOut()
 */
abstract class FrontPresenter extends Presenter
{
	/**
	 * {@inheritdoc}
	 *
	 * @throws \Nette\Application\AbortException
	 */
	protected function onForbiddenRequest(AuthorizationAnnotationInterface $annotation): void
	{
		if ($annotation instanceof LoggedOut) {
			$this->redirect(':Admin:Dashboard:');
		}
	}
}
