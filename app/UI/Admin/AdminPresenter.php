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
        $form = new Form;

        $form->addText('username', 'Username:')
            ->setRequired();

        $form->addPassword('password', 'Password:')
            ->setRequired();

        $form->addEmail('email', 'Email:')
            ->setRequired();

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
        $form = new Form;

        $form->addText('username', 'Username:')
            ->setRequired();
        $form->addEmail('email', 'Email:')
            ->setRequired();
        $form->addPassword('password', 'Password:');
        $form->addSubmit('send', 'Save Changes');

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
        $userId = $this->getParameter('id');

        if (!$this->userManager->isUsernameUnique($values->username, $userId)) {
            $form->addError('Username already exists.');
            return;
        }

        // Check if email is unique
        if (!$this->userManager->isEmailUnique($values->email, $userId)) {
            $form->addError('Email already exists.');
            return;
        }

        $this->userManager->updateUser($userId, $values->username, $values->password, $values->email);
        $this->flashMessage('User updated');
        $this->redirect('Admin:users');
    }
}
