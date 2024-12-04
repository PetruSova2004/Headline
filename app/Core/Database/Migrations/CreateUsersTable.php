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
            ['username' => 'user4', 'email' => 'user4@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user5', 'email' => 'user5@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user6', 'email' => 'user6@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user7', 'email' => 'user7@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user8', 'email' => 'user8@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user9', 'email' => 'user9@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user10', 'email' => 'user10@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user11', 'email' => 'user11@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user12', 'email' => 'user12@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user13', 'email' => 'user13@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user14', 'email' => 'user14@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
            ['username' => 'user15', 'email' => 'user15@example.com', 'password' => password_hash('user123', PASSWORD_BCRYPT), 'role_id' => 2],
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
