<?php

namespace App\Core\Services;

use Nette\Mail\SmtpMailer;
use Nette\Mail\Message;

class EmailService
{

    /**
     * @param SmtpMailer $mailer
     */
    public function __construct(private readonly SmtpMailer $mailer)
    {}

    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string $from
     * @return void
     */
    public function sendEmail(string $to, string $subject, string $body, string $from = 'no-reply@example.com'): void
    {
        $mail = new Message();
        $mail->setFrom($from)
            ->addTo($to)
            ->setSubject($subject)
            ->setHtmlBody($body);

        $this->mailer->send($mail);
    }
}