<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Nette\Database\Explorer;
use Nette\Application\UI\Presenter;

class RegistrationFormFactory
{
    private readonly Explorer $database;
    private readonly Passwords $passwords;

    public function __construct(
        Explorer $database,
        Passwords $passwords
    ) {
        $this->database = $database;
        $this->passwords = $passwords;
    }

    public function create(Presenter $presenter): Form
    {
        $form = new Form();

        $form->addText('username', 'Username:')
            ->setRequired('Please enter your username.')
            ->addRule($form::MIN_LENGTH, 'Username must be at least %d characters long.', 3);

        $form->addEmail('email', 'Email:')
            ->setRequired('Please enter your email.');

        $form->addPassword('password', 'Password:')
            ->setRequired('Please enter your password.')
            ->addRule($form::MIN_LENGTH, 'Password must be at least %d characters long.', 6);

        $form->addPassword('password_confirm', 'Confirm Password:')
            ->setRequired('Please confirm your password.')
            ->addRule($form::EQUAL, 'Passwords do not match.', $form['password']);

        $form->addSubmit('register', 'Register');

        $form->onSuccess[] = function (Form $form, array $values) use ($presenter) {
            $this->processForm($form, $values, $presenter);  // Передаем Presenter
        };

        return $form;
    }

    public function processForm(Form $form, array $values, Presenter $presenter): void
    {
        if ($this->database->table('users')->where('username', $values['username'])->fetch()) {
            $form->addError('Username already exists.');
            return;
        }

        if ($this->database->table('users')->where('email', $values['email'])->fetch()) {
            $form->addError('Email already exists.');
            return;
        }

        // Insert new user into the database
        $this->database->table('users')->insert([
            'username' => $values['username'],
            'email' => $values['email'],
            'password' => $this->passwords->hash($values['password']),
            'role_id' => 2, // Default user role
        ]);

        $presenter->flashMessage('Registration successful! You can now log in.', 'success');
        $presenter->redirect('Home:default');
    }
}
