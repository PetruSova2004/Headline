<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;
use Nette\Security\User;

class LoginFormFactory
{

    /**
     * @param User $user
     */
    public function __construct(private readonly User $user)
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
            $this->user->login($values['username'], $values['password']);
            $presenter->flashMessage('You have successfully logged in', 'success');
            $presenter->redirect('Home:default');
        } catch (AuthenticationException) {
            $form->addError('Invalid credentials.');
        }
    }
}
