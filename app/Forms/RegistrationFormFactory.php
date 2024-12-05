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
            <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333;'>
                <table align='center' cellpadding='0' cellspacing='0' style='width: 100%; max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                    <tr>
                        <td style='padding: 20px; text-align: center; background-color: #007bff; color: white; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                            <h1 style='margin: 0;'>Welcome to Our Service</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 20px;'>
                            <p style='font-size: 18px; margin: 0;'>Hello, <strong>$username</strong>!</p>
                            <p style='font-size: 16px; line-height: 1.5; margin: 15px 0;'>
                                Thank you for joining us! To complete your registration and verify your email address, click the button below:
                            </p>
                            <div style='text-align: center; margin: 20px 0;'>
                                <a href=\"$verificationUrl\" 
                                   style='display: inline-block; background-color: #007bff; color: white; padding: 12px 25px; font-size: 16px; font-weight: bold; text-decoration: none; border-radius: 25px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);'>
                                   Verify Email Address
                                </a>
                            </div>
                            <p style='font-size: 16px; line-height: 1.5;'>
                                If you didn’t register for an account, please ignore this email. If you have any questions, feel free to contact us at <a href='mailto:support@example.com' style='color: #007bff; text-decoration: none;'>support@example.com</a>.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 20px; text-align: center; background-color: #f4f4f4; color: #777; font-size: 14px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;'>
                            <p style='margin: 0;'>© " . date('Y') . " Our Service. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </body>
        </html>
    ";
    }



}
