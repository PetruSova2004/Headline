<?php


namespace App\Model;

use Nette\Database\Context;

class UserManager
{

    /**
     * @param Context $database
     */
    public function __construct(private readonly Context $database)
    {}

    /**
     * @return array
     */
    public function getAllUsers(): array
    {
        return $this->database->table('users')->fetchAll();
    }

    /**
     * @return int
     */
    public function getTotalUsers(): int
    {
        return $this->database->table('users')->count();
    }

    /**
     * @param $page
     * @param $itemsPerPage
     * @param $currentUserId
     * @return array
     */
    public function getUsersPaginated($page, $itemsPerPage, $currentUserId): array
    {
        if (is_numeric($page)) {
            $page = intval($page);
        }

        return $this->database->table('users')
            ->where('id != ?', $currentUserId)
            ->order('id DESC')
            ->limit($itemsPerPage, ($page - 1) * $itemsPerPage)  // Apply both limit and offset
            ->fetchAll();
    }



    /**
     * @param int $id
     * @return mixed
     */
    public function getUserById(int $id): mixed
    {
        return $this->database->table('users')->get($id);
    }

    /**
     * @param string $username
     * @param $excludeId
     * @return bool
     */
    public function isUsernameUnique(string $username, $excludeId = null): bool
    {
        $query = $this->database->table('users')->where('username', $username);

        if ($excludeId) {
            $query->where('id != ?', $excludeId);
        }

        return $query->count() === 0;
    }


    /**
     * @param string $email
     * @param $excludeId
     * @return bool
     */
    public function isEmailUnique(string $email, $excludeId = null): bool
    {
        $query = $this->database->table('users')->where('email', $email);

        if ($excludeId) {
            $query->where('id != ?', $excludeId);
        }

        return $query->count() === 0;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @return void
     */
    public function createUser(string $username, string $password, string $email): void
    {
        $this->database->table('users')->insert([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'email' => $email,
            'role_id' => 2
        ]);
    }

    /**
     * @param $id
     * @param string $username
     * @param string|null $password
     * @param string $email
     * @return void
     */
    public function updateUser($id, string $username, ?string $password, string $email): void
    {
        $data = [
            'username' => $username,
            'email' => $email,
        ];

        if ($password) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->database->table('users')->where('id', $id)->update($data);
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteUser(int $id): void
    {
        $this->database->table('users')->where('id', $id)->delete();
    }
}
