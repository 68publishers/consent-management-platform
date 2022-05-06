<?php

declare(strict_types=1);

namespace App\Web\Ui;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Nette\Security\User as NetteUser;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface;
use SixtyEightPublishers\EventDispatcherExtra\EventDispatcherAwareTrait;
use SixtyEightPublishers\SmartNetteComponent\UI\Control as SmartControl;
use SixtyEightPublishers\EventDispatcherExtra\EventDispatcherAwareInterface;
use SixtyEightPublishers\FlashMessageBundle\Bridge\Nette\Ui\ControlTrait as FlashMessageControlTrait;

abstract class Control extends SmartControl implements TranslatorAwareInterface, EventDispatcherAwareInterface, LoggerAwareInterface
{
	use TranslatorAwareTrait;
	use RedrawControlTrait;
	use EventDispatcherAwareTrait;
	use FlashMessageControlTrait;
	use LoggerAwareTrait;

	private NetteUser $user;

	/**
	 * @internal
	 * @param \Nette\Security\User $user
	 *
	 * @return void
	 */
	public function injectUser(NetteUser $user): void
	{
		$this->user = $user;
	}

	/**
	 * @return void
	 */
	public function render(): void
	{
		$this->template->setTranslator($this->getPrefixedTranslator());
		$this->doRender();
	}

	/**
	 * @return \Nette\Security\User
	 */
	public function getUser(): NetteUser
	{
		return $this->user;
	}
}
