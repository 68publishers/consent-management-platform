<?php

declare(strict_types=1);

namespace App\Web\Ui;

use Nette\Application\Request;
use App\Web\Control\Gtm\GtmControl;
use App\Web\Ui\Form\RecaptchaResolver;
use Nette\Application\Responses\ForwardResponse;
use App\Web\Control\Gtm\GtmControlFactoryInterface;
use Nette\Application\UI\Presenter as NettePresenter;
use App\Web\Ui\Modal\PresenterTrait as ModalPresenterTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface;
use SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\AuthorizationTrait;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;
use SixtyEightPublishers\FlashMessageBundle\Bridge\Nette\Ui\PresenterTrait as FlashMessagePresenterTrait;

abstract class Presenter extends NettePresenter implements TranslatorAwareInterface, ComponentAuthorizatorAwareInterface
{
	use TranslatorAwareTrait;
	use RedrawControlTrait;
	use FlashMessagePresenterTrait;
	use ModalPresenterTrait;
	use AuthorizationTrait;

	private TranslatorLocalizerInterface $translatorLocalizer;

	private GtmControlFactoryInterface $gtmControlFactory;

	private RecaptchaResolver $recaptchaResolver;

	/**
	 * @internal
	 *
	 * @param \SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface $translatorLocalizer
	 * @param \App\Web\Control\Gtm\GtmControlFactoryInterface                                   $gtmControlFactory
	 * @param \App\Web\Ui\Form\RecaptchaResolver                                                $recaptchaResolver
	 *
	 * @return void
	 */
	public function injectBaseDependencies(TranslatorLocalizerInterface $translatorLocalizer, GtmControlFactoryInterface $gtmControlFactory, RecaptchaResolver $recaptchaResolver): void
	{
		$this->translatorLocalizer = $translatorLocalizer;
		$this->gtmControlFactory = $gtmControlFactory;
		$this->recaptchaResolver = $recaptchaResolver;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function beforeRender(): void
	{
		$template = $this->getTemplate();

		$template->setTranslator($this->getPrefixedTranslator());

		$template->locale = $this->translatorLocalizer->getLocale();
		$template->lang = current(explode('_', $this->translatorLocalizer->getLocale()));
		$template->user = $this->getUser();
		$template->recaptchaEnabled = $this->recaptchaResolver->isEnabled();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Nette\Application\AbortException
	 */
	public function restoreRequest($key): void
	{
		$session = $this->getSession('Nette.Application/requests');

		if (!isset($session[$key]) || ($session[$key][0] !== NULL && (string) $session[$key][0] !== (string) $this->getUser()->getId())) {
			return;
		}

		/** @var \Nette\Application\Request $request */
		$request = clone $session[$key][1];

		unset($session[$key]);

		$request->setFlag(Request::RESTORED, TRUE);

		$params = $request->getParameters();
		$params[self::FLASH_KEY] = $this->getFlashKey();

		$request->setParameters($params);
		$this->sendResponse(new ForwardResponse($request));
	}

	/**
	 * @return \App\Web\Control\Gtm\GtmControl
	 */
	protected function createComponentGtm(): GtmControl
	{
		return $this->gtmControlFactory->create();
	}

	/**
	 * @return string|NULL
	 */
	private function getFlashKey(): ?string
	{
		$flashKey = $this->getParameter(self::FLASH_KEY);

		return is_string($flashKey) && $flashKey !== ''
			? $flashKey
			: NULL;
	}
}
