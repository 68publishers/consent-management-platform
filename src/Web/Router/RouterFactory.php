<?php

declare(strict_types=1);

namespace App\Web\Router;

use Nette\Routing\Route;
use Nette\Application\Routers\RouteList;
use App\Application\Localization\Profiles;
use App\Web\AdminModule\Presenter\SignOutPresenter;
use SixtyEightPublishers\UserBundle\Application\Csrf\CsrfTokenFactoryInterface;

final class RouterFactory
{
	public function __construct(
		private readonly Profiles $profiles,
		private readonly CsrfTokenFactoryInterface $csrfTokenFactory,
	) {
	}

	/**
	 * @return \Nette\Application\Routers\RouteList
	 */
	public function create(): RouteList
	{
		$router = new RouteList();

		$router->withModule('Front')
			->addRoute('[<locale [a-z]{2}>/]<presenter>[/<id>]', [
				NULL => [
					Route::FILTER_IN => function (array $params) {
						if (!in_array($params['presenter'], [
							'SignIn',
							'ForgotPassword',
							'ResetPassword',
						], TRUE)) {
							return NULL;
						}

						if (isset($params['locale']) && !$this->profiles->has($params['locale'])) {
							$params['locale'] = $this->profiles->default()->locale();
						}

						return $params;
					},
				],
				'action' => 'default',
				'locale' => $this->profiles->default()->locale(),
			]);

		$router->addRoute('project/<project>/[<module>/]<presenter>[/<id>]', [
			'module' => 'Admin:Project',
			'action' => 'default',
		]);

		$router->withModule('Admin')
			->addRoute('[[<module>/]<presenter>[/<id>]]', [
				'presenter' => 'Dashboard',
				'action' => 'default',
				NULL => [
					Route::FilterOut => function (array $parameters) {
						if ('SignOut' === ($parameters['presenter'] ?? NULL)) {
							$parameters['_sec'] = $this->csrfTokenFactory->create(SignOutPresenter::class);
						}

						return $parameters;
					},
				],
			]);

		return $router;
	}
}
