<?php

declare(strict_types=1);

namespace App\Web\Router;

use Nette\Routing\Route;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
	/**
	 * @return \Nette\Application\Routers\RouteList
	 */
	public function create(): RouteList
	{
		$router = new RouteList();

		$router->withModule('Front')
			->addRoute('<presenter>', [
				'action' => 'default',
				NULL => [
					Route::FILTER_IN => static function (array $params) {
						return in_array($params['presenter'], [
							'SignIn',
							'SignUp',
							'ForgotPassword',
							'ResetPassword',
						], TRUE) ? $params : NULL;
					},
				],
			]);

		$router->withModule('Admin')
			->addRoute('[/<module>/<presenter>[/<id>]]', [
				'presenter' => 'Dashboard',
				'action' => 'default',
			])
			->addRoute('<presenter>/<_sec>', [
				'presenter' => 'SignOut',
				'action' => 'default',
			]);

		return $router;
	}
}
