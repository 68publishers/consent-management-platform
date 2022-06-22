<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm;

use App\ReadModel\Cookie\CookieView;
use App\Web\Ui\Modal\AbstractModalControl;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

final class CookieFormModalControl extends AbstractModalControl
{
	private ValidLocalesProvider $validLocalesProvider;

	private CookieProviderId $cookieProviderId;

	private ?CookieView $default;

	private CookieFormControlFactoryInterface $cookieFormControlFactory;

	/**
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider                                   $validLocalesProvider
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId                                $cookieProviderId
	 * @param \App\ReadModel\Cookie\CookieView|NULL                                                  $default
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormControlFactoryInterface $cookieFormControlFactory
	 */
	public function __construct(ValidLocalesProvider $validLocalesProvider, CookieProviderId $cookieProviderId, ?CookieView $default, CookieFormControlFactoryInterface $cookieFormControlFactory)
	{
		$this->validLocalesProvider = $validLocalesProvider;
		$this->cookieProviderId = $cookieProviderId;
		$this->default = $default;
		$this->cookieFormControlFactory = $cookieFormControlFactory;
	}

	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormControl
	 */
	public function getInnerControl(): CookieFormControl
	{
		return $this->getComponent('cookieForm');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->default = $this->default;
	}

	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormControl
	 */
	protected function createComponentCookieForm(): CookieFormControl
	{
		return $this->cookieFormControlFactory->create($this->validLocalesProvider, $this->cookieProviderId, $this->default);
	}
}
