<?php

namespace App\Security;

use Nette\Security\Authenticator;
use Nette\Security\SimpleIdentity;
use Nette\Security\AuthenticationException;
use Nette\Database\Explorer;

class SecurityAuthenticator implements Authenticator
{

    /**
     * @param Explorer $database
     */
    public function __construct(private readonly Explorer $database)
    {}

    /**
     * @param string $username
     * @param string $password
     * @return SimpleIdentity
     * @throws AuthenticationException
     */
    public function authenticate(string $username, string $password): SimpleIdentity
    {
        $user = $this->database->table('users')->where('username', $username)->fetch();

        if (!$user) {
            throw new AuthenticationException('User not found.');
        }

        if (!password_verify($password, $user->password)) {
            throw new AuthenticationException('Invalid password.');
        }

        return new SimpleIdentity(
            $user->id,
            ['role' => $user->ref('roles', 'role_id')->name],
            ['username' => $user->username, 'email' => $user->email]
        );
    }
}
