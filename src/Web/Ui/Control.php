<?php

declare(strict_types=1);

namespace App\Web\Ui;

use Nette\Security\User as NetteUser;
use SixtyEightPublishers\NotificationBundle\UI\TNotifier;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface;
use SixtyEightPublishers\SmartNetteComponent\UI\Control as SmartControl;

abstract class Control extends SmartControl implements TranslatorAwareInterface
{
	use TNotifier;
	use TranslatorAwareTrait;
	use RedrawControlTrait;

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
