<?php

namespace App\UI\Auth;

use App\Forms\LoginFormFactory;
use App\Forms\RegistrationFormFactory;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

class AuthPresenter extends Presenter
{
    /**
     * @param LoginFormFactory $loginFormFactory
     * @param RegistrationFormFactory $registrationFormFactory
     */
    public function __construct(private readonly LoginFormFactory $loginFormFactory, private readonly RegistrationFormFactory $registrationFormFactory)
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function actionLogin(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->flashMessage('You have already logged in', 'danger');
            $this->redirect('Home:default'); // Redirect to home if already logged in
        }
    }

    /**
     * @return void
     */
    public function actionRegister(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->flashMessage('You have already logged in', 'danger');
            $this->redirect('Home:default');
        }
    }


    /**
     * @return void
     */
    #[NoReturn] public function actionLogout(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->getUser()->logout();
            $this->flashMessage('You have successfully logged out', 'success');
        } else {
            $this->flashMessage('You must be logged in to log out', 'danger');
        }
        $this->redirect('Auth:login');
    }

    /**
     * @return Form
     */
    public function createComponentLoginForm(): Form
    {
        return $this->loginFormFactory->create($this);
    }

    /**
     * @return Form
     */
    public function createComponentRegistrationForm(): Form
    {
        return $this->registrationFormFactory->create($this);
    }

}
