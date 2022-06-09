<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\Ui\Presenter;
use App\Web\Control\Footer\FooterControl;
use App\Web\Control\Localization\LocalizationControl;
use App\Web\Control\Footer\FooterControlFactoryInterface;
use App\Web\Control\Localization\Event\ProfileChangedEvent;
use App\Web\Control\Localization\Event\ProfileChangeFailed;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Annotation\LoggedOut;
use App\Web\Control\Localization\LocalizationControlFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface;

/**
 * @LoggedOut()
 */
abstract class FrontPresenter extends Presenter
{
	/** @persistent */
	public string $locale;

	private FooterControlFactoryInterface $footerControlFactory;

	private LocalizationControlFactoryInterface $localizationControlFactory;

	/**
	 * @param \App\Web\Control\Footer\FooterControlFactoryInterface             $footerControlFactory
	 * @param \App\Web\Control\Localization\LocalizationControlFactoryInterface $localizationControlFactory
	 *
	 * @return void
	 */
	public function injectFrontDependencies(FooterControlFactoryInterface $footerControlFactory, LocalizationControlFactoryInterface $localizationControlFactory): void
	{
		$this->footerControlFactory = $footerControlFactory;
		$this->localizationControlFactory = $localizationControlFactory;
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

	/**
	 * @return \App\Web\Control\Localization\LocalizationControl
	 */
	protected function createComponentLocalization(): LocalizationControl
	{
		$control = $this->localizationControlFactory->create();

		$control->addEventListener(ProfileChangedEvent::class, function (ProfileChangedEvent $event): void {
			$this->redirect('this', [
				'locale' => $event->profile()->locale(),
			]);
		});

		$control->addEventListener(ProfileChangeFailed::class, function (): void {
			$this->subscribeFlashMessage(FlashMessage::error('//layout.message.locale_change_failed'));
		});

		return $control;
	}
}
