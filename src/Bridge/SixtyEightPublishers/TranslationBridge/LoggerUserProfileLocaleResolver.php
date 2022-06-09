<?php

declare(strict_types=1);

namespace App\Bridge\SixtyEightPublishers\TranslationBridge;

use App\ReadModel\User\UserView;
use Nette\Localization\Translator;
use Nette\Security\User as NetteUser;
use SixtyEightPublishers\UserBundle\Bridge\Nette\Security\Identity;
use SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocaleResolverInterface;

final class LoggerUserProfileLocaleResolver implements TranslatorLocaleResolverInterface
{
	private NetteUser $user;

	/**
	 * @param \Nette\Security\User $user
	 */
	public function __construct(NetteUser $user)
	{
		$this->user = $user;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\UserBundle\Application\Exception\IdentityException
	 */
	public function resolveLocale(Translator $translator): ?string
	{
		if (!$this->user->isLoggedIn()) {
			return NULL;
		}

		$identity = $this->user->getIdentity();

		if (!$identity instanceof Identity) {
			return NULL;
		}

		$data = $identity->data();
		assert($data instanceof UserView);

		return $data->profileLocale->value();
	}
}
