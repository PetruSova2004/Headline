<?php

declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

        $router->addRoute('home', 'Home:default');
        $router->addRoute('register', 'Auth:register');
        $router->addRoute('login', 'Auth:login');
        $router->addRoute('logout', 'Auth:logout');

        $router->addRoute('admin/users', 'Admin:users');
        $router->addRoute('admin/users/create', 'Admin:create');
        $router->addRoute('admin/users/<id>/edit', 'Admin:edit');
        $router->addRoute('admin/users/<id>/delete', 'Admin:delete');

        $router->addRoute('<action>', 'Home:default');

		return $router;
	}
}
