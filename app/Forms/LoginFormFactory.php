<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Explorer;
use Nette\Security\AuthenticationException;
use Nette\Security\User;

class LoginFormFactory
{

    /**
     * @param User $user
     * @param Explorer $database
     */
    public function __construct(
        private readonly User $user,
        private readonly Explorer     $database,
    )
    {}

    /**
     * @param Presenter $presenter
     * @return Form
     */

    public function create(Presenter $presenter): Form
    {
        $form = new Form();

        $form->addText('username', 'Username:')
            ->setRequired('Please enter your username.');

        $form->addPassword('password', 'Password:')
            ->setRequired('Please enter your password.');

        $form->addSubmit('login', 'Login');

        $form->onSuccess[] = function (Form $form, array $values) use ($presenter) {
            $this->processForm($form, $values, $presenter);
        };
        return $form;
    }

    /**
     * @param Form $form
     * @param array $values
     * @param Presenter $presenter
     * @return void
     */
    public function processForm(Form $form, array $values, Presenter $presenter): void
    {
        try {
            $loginUser = $this->database->table('users')->where('username', $values['username'])->fetch();

            // Check if the user exists and if the user is verified
            if ($loginUser && ($loginUser->verified !== 1)) {
                $presenter->flashMessage('You need to verify your email before logging in.', 'danger');
                $presenter->redirect('Auth:login');
            }

            $this->user->login($values['username'], $values['password']);
            $presenter->flashMessage('You have successfully logged in', 'success');
            $presenter->redirect('Home:default');
        } catch (AuthenticationException) {
            $form->addError('Invalid credentials.');
        }
    }
}
