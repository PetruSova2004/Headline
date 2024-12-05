<?php

declare(strict_types=1);

namespace App\UI\Admin;

use App\Model\UserManager;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Utils\Paginator;
use stdClass;

class AdminPresenter extends Presenter
{

    /**
     * @param UserManager $userManager
     */
    public function __construct(private readonly UserManager $userManager)
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function renderUsers(): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Auth:login');
        }
        $itemsPerPage = 10;

        $page = $this->getParameter('page', 1);
        $page = (int) $page;

        $totalUsers = $this->userManager->getTotalUsers();
        $currentUserId = $this->getUser()->getId(); // Our id


        $users = $this->userManager->getUsersPaginated($page, $itemsPerPage, $currentUserId);

        $this->template->users = $users;
        $this->template->pagination = new Paginator;
        $this->template->pagination->setItemCount($totalUsers);
        $this->template->pagination->setItemsPerPage($itemsPerPage);
        $this->template->pagination->setPage($page);
    }

    /**
     * @param int $id
     * @return void
     */
    public function renderEdit(int $id): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Auth:login');
        }
        $user = $this->userManager->getUserById($id);
        if (!$user) {
            $this->error('User not found');
        }
        $this->template->foreignUser = $user;
    }

    /**
     * @param int $id
     * @return void
     */
    #[NoReturn] public function actionDelete(int $id): void
    {
        $this->userManager->deleteUser($id);
        $this->flashMessage('User deleted');
        $this->redirect('users');
    }

    /**
     * @return Form
     */
    protected function createComponentCreateForm(): Form
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Auth:login');
        }
        $form = new Form;

        $form->addText('username', 'Username:')
            ->setRequired('Username is required.')
            ->addRule($form::MIN_LENGTH, 'Username must be at least %d characters.', 3)
            ->addRule($form::MAX_LENGTH, 'Username must be no more than %d characters.', 20);

        $form->addPassword('password', 'Password:')
            ->setRequired('Password is required.')
            ->addRule($form::MIN_LENGTH, 'Password must be at least %d characters.', 8)
            ->addRule($form::MAX_LENGTH, 'Password must be no more than %d characters.', 25);

        $form->addEmail('email', 'Email:')
            ->setRequired('Email is required.')
            ->addRule($form::EMAIL, 'Invalid email format.');

        $form->addSubmit('send', 'Create User');

        $form->onSuccess[] = [$this, 'createFormSucceeded'];
        return $form;
    }

    /**
     * @param Form $form
     * @param stdClass $values
     * @return void
     */
    #[NoReturn] public function createFormSucceeded(Form $form, stdClass $values): void
    {
        $userId = $this->getParameter('id');

        if (!$this->userManager->isUsernameUnique($values->username, $userId)) {
            $form->addError('Username already exists.');
            return;
        }
        if (!$this->userManager->isEmailUnique($values->email, $userId)) {
            $form->addError('Email already exists.');
            return;
        }

        $this->userManager->createUser($values->username, $values->password, $values->email);
        $this->flashMessage('User created');
        $this->redirect('users');
    }

    /**
     * @return Form
     */
    protected function createComponentEditForm(): Form
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Auth:login');
        }

        $userId = (int)$this->getParameter('id');
        $user = $this->userManager->getUserById($userId);

        if (!$user) {
            $this->error('User not found');
        }

        $form = new Form;

        $form->addText('username', 'Username:')
            ->setRequired('Username is required.')
            ->addRule($form::MIN_LENGTH, 'Username must be at least %d characters.', 3)
            ->addRule($form::MAX_LENGTH, 'Username must be no more than %d characters.', 20);

        $form->addEmail('email', 'Email:')
            ->setRequired('Email is required.')
            ->addRule($form::EMAIL, 'Invalid email format.');

        $form->addPassword('password', 'Password:')
            ->addRule($form::MIN_LENGTH, 'Password must be at least %d characters if provided.', 8)
            ->addRule(
                function ($input) {
                    return !str_contains($input->getValue(), ' ');
                },
                'Password must not contain spaces.'
            );

        $form->addSubmit('send', 'Save Changes');

        $form->setDefaults([
            'username' => $user->username,
            'email' => $user->email,
        ]);

        $form->onSuccess[] = [$this, 'editFormSucceeded'];
        return $form;
    }




    /**
     * @param Form $form
     * @param stdClass $values
     * @return void
     */
    #[NoReturn] public function editFormSucceeded(Form $form, stdClass $values): void
    {
        $userId = (int)$this->getParameter('id');

        $existingUser = $this->userManager->getUserById($userId);
        if (!$existingUser) {
            $this->error('User not found');
        }

        // Проверка на уникальность имени и email
        if (
            $values->username !== $existingUser->username &&
            !$this->userManager->isUsernameUnique($values->username, $userId)
        ) {
            $form->addError('Username already exists.');
            return;
        }

        if (
            $values->email !== $existingUser->email &&
            !$this->userManager->isEmailUnique($values->email, $userId)
        ) {
            $form->addError('Email already exists.');
            return;
        }

        // Проверить, был ли введен новый пароль
        $password = $values->password ?: $existingUser->password;

        // Если изменения отсутствуют, просто перенаправить
        if (
            $values->username === $existingUser->username &&
            $values->email === $existingUser->email &&
            $password === $existingUser->password
        ) {
            $this->flashMessage('No changes made');
            $this->redirect('Admin:users');
        }

        // Обновление данных
        $this->userManager->updateUser($userId, $values->username, $password, $values->email);
        $this->flashMessage('User updated');
        $this->redirect('Admin:users');
    }
}
