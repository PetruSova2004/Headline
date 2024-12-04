<?php

require __DIR__ . '/../../../vendor/autoload.php';

use App\Bootstrap;
use App\Core\Database\Migrations\MigrationRunner;
use Nette\Database\Explorer;

$bootstrap = new Bootstrap();
$container = $bootstrap->bootWebApplication();

$database = $container->getByType(Explorer::class);

$migrationRunner = new MigrationRunner($database);
$migrationRunner->runMigrations();
