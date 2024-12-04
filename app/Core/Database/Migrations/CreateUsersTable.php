<?php

namespace App\Core\Database\Migrations;

use Nette\Database\Explorer;

class CreateUsersTable
{

    /**
     * @param Explorer $database
     */
    public function __construct(private readonly Explorer $database)
    {
    }

    /**
     * @return void
     */
    public function up(): void
    {
        $this->database->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
            );
        ");

        $this->database->table('users')->insert([
            ['username' => 'admin', 'email' => 'admin@example.com', 'password' => password_hash('admin123', PASSWORD_BCRYPT), 'role_id' => 1],
            ['username' => 'user1', 'email' => 'user1@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user2', 'email' => 'user2@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user3', 'email' => 'user3@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
        ]);
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->database->query("DROP TABLE IF EXISTS users;");
    }
}
