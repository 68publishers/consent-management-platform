<?php

declare(strict_types=1);

namespace App\Web\Router;

use Nette\Routing\Route;
use Nette\Application\Routers\RouteList;
use App\Application\Localization\Profiles;

final class RouterFactory
{
	private Profiles $profiles;

	/**
	 * @param \App\Application\Localization\Profiles $profiles
	 */
	public function __construct(Profiles $profiles)
	{
		$this->profiles = $profiles;
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
			]);

		return $router;
	}
}
