<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\Ui\Presenter;
use App\Web\Control\Footer\FooterControl;
use App\Web\Control\Footer\FooterControlFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\LoggedOut;
use SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface;

/**
 * @LoggedOut()
 */
abstract class FrontPresenter extends Presenter
{
	private FooterControlFactoryInterface $footerControlFactory;

	/**
	 * @param \App\Web\Control\Footer\FooterControlFactoryInterface $footerControlFactory
	 *
	 * @return void
	 */
	public function injectFrontDependencies(FooterControlFactoryInterface $footerControlFactory): void
	{
		$this->footerControlFactory = $footerControlFactory;
	}

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

	/**
	 * @return \App\Web\Control\Footer\FooterControl
	 */
	protected function createComponentFooter(): FooterControl
	{
		return $this->footerControlFactory->create();
	}
}
