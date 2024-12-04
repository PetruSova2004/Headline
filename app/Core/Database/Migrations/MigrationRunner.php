<?php

namespace App\Core\Database\Migrations;

use Nette\Database\Explorer;

class MigrationRunner
{

    /**
     * @param Explorer $database
     */
    public function __construct(private readonly Explorer $database)
    {}

    /**
     * @return void
     */
    public function runMigrations(): void
    {
        $createRolesTable = new CreateRolesTable($this->database);
        $createRolesTable->up();

        $createUsersTable = new CreateUsersTable($this->database);
        $createUsersTable->up();

        echo "Migrations completed successfully! \n";
    }
}
