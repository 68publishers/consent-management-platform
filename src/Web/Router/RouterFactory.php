<?php

declare(strict_types=1);

namespace App\Web\Router;

use Nette\Routing\Route;
use Nette\Application\Routers\RouteList;
use App\Application\OpenApiConfiguration;

final class RouterFactory
{
	private OpenApiConfiguration $openApiConfiguration;

	/**
	 * @param \App\Application\OpenApiConfiguration $openApiConfiguration
	 */
	public function __construct(OpenApiConfiguration $openApiConfiguration)
	{
		$this->openApiConfiguration = $openApiConfiguration;
	}

	/**
	 * @return \Nette\Application\Routers\RouteList
	 */
	public function create(): RouteList
	{
		$router = new RouteList();

		if ($this->openApiConfiguration->enabled()) {
			$router->addRoute('swagger', [
				'module' => 'Swagger',
				'presenter' => 'Swagger',
				'action' => 'default',
			]);
		}

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
