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
			->addRoute('<presenter>[/<id>]', [
				NULL => [
					Route::FILTER_IN => static function (array $params) {
						return in_array($params['presenter'], [
							'SignIn',
							'ForgotPassword',
							'ResetPassword',
						], TRUE) ? $params : NULL;
					},
				],
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
