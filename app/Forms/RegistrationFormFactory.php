<?php

namespace App\Forms;

use App\Core\Services\EmailService;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Nette\Database\Explorer;
use Nette\Application\UI\Presenter;

class RegistrationFormFactory
{

    /**
     * @param Explorer $database
     * @param Passwords $passwords
     * @param EmailService $emailService
     */
    public function __construct(
        private readonly Explorer     $database,
        private readonly Passwords    $passwords,
        private readonly EmailService $emailService,
    )
    {
    }

    /**
     * @param Presenter $presenter
     * @return Form
     */
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
            $this->processForm($form, $values, $presenter);
        };

        return $form;
    }

    /**
     * @param Presenter $presenter
     * @param string $token
     * @return void
     */
    #[NoReturn] public function verify(Presenter $presenter, string $token): void
    {
        $user = $this->database->table('users')->where('verification_token', $token)->fetch();

        if ($user) {
            $this->database->table('users')->where('id', $user->id)->update([
                'verified' => 1,
                'verification_token' => null,
                'verification_sent_at' => null
            ]);

            $presenter->flashMessage('Your email has been successfully verified. You can now log in.', 'success');
        } else {
            $presenter->flashMessage('Invalid or expired verification link.', 'error');
        }

        $presenter->redirect('Auth:login');
    }

    /**
     * @param Form $form
     * @param array $values
     * @param Presenter $presenter
     * @return void
     */
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

        $verificationToken = bin2hex(random_bytes(32));

        $this->database->table('users')->insert([
            'username' => $values['username'],
            'email' => $values['email'],
            'password' => $this->passwords->hash($values['password']),
            'role_id' => 2,
            'verification_token' => $verificationToken,
        ]);

        $body = $this->generateVerificationEmailBody($values['username'], $values['email'], $verificationToken);

        $to = $values['email'];
        $subject = 'Register Confirmation';
        $this->emailService->sendEmail($to, $subject, $body);

        $presenter->flashMessage('Registration successful! Please check your email to confirm your registration.', 'success');
        $presenter->redirect('Auth:login');
    }


    /**
     * @param string $username
     * @param string $email
     * @param string $verificationToken
     * @return string
     */
    public function generateVerificationEmailBody(string $username, string $email, string $verificationToken): string
    {
        $baseUrl = getenv('APP_URL') ?: 'http://localhost:8000';

        $verificationUrl = "$baseUrl/auth/verify?token=$verificationToken";

        return "
            <html>
                <body style='font-family: Arial, sans-serif;'>
                    <p>Hello, $username!</p>
                    <p>Thank you for registering with us. Please confirm your email address by clicking the button below:</p>
                    <table role='presentation' style='width: 100%; border: none;'>
                        <tr>
                            <td style='text-align: center;'>
                                <a href=\"$verificationUrl\" 
                                   style='background-color: #007bff; color: white; padding: 10px 20px; font-size: 16px; text-decoration: none; border-radius: 5px;'>
                                   Confirm your email address
                                </a>
                            </td>
                        </tr>
                    </table>
                    <p>If you did not register, please ignore this email.</p>
                </body>
            </html>
        ";
    }


}
