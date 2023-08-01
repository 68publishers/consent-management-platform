<?php

declare(strict_types=1);

namespace App\Web\Ui;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Nette\Security\User as NetteUser;
use Nette\Application\UI\Control as NetteControl;
use App\Web\Ui\Modal\ControlTrait as ModalControlTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareTrait;
use SixtyEightPublishers\TranslationBridge\TranslatorAwareInterface;
use SixtyEightPublishers\EventDispatcherExtra\EventDispatcherAwareTrait;
use SixtyEightPublishers\EventDispatcherExtra\EventDispatcherAwareInterface;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\AuthorizationTrait;
use SixtyEightPublishers\SmartNetteComponent\Bridge\Nette\Application\TemplateResolverTrait;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorAwareInterface;
use SixtyEightPublishers\FlashMessageBundle\Bridge\Nette\Ui\ControlTrait as FlashMessageControlTrait;

abstract class Control extends NetteControl implements TranslatorAwareInterface, EventDispatcherAwareInterface, LoggerAwareInterface, ComponentAuthorizatorAwareInterface
{
	use TranslatorAwareTrait;
	use RedrawControlTrait;
	use EventDispatcherAwareTrait;
	use FlashMessageControlTrait;
	use LoggerAwareTrait;
	use ModalControlTrait;
	use TemplateResolverTrait;
	use AuthorizationTrait;

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
	protected function beforeRender(): void
	{
		$this->getTemplate()->setTranslator($this->getPrefixedTranslator());
	}

	/**
	 * @return \Nette\Security\User
	 */
	public function getUser(): NetteUser
	{
		return $this->user;
	}
}
