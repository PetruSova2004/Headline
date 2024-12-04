<?php

namespace App\Core\Database\Migrations;

use Nette\Database\Explorer;

class CreateRolesTable
{
    /**
     * @param Explorer $database
     */
    public function __construct(private readonly Explorer $database)
    {}

    /**
     * @return void
     */
    public function up(): void
    {
        $this->database->query("
            CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
        ");

        $this->database->table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'user']
        ]);
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->database->query("DROP TABLE IF EXISTS roles;");
    }
}
