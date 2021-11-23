<?php 
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send email
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $pathtemplate
     * @param array $parameters
     * @return void
     */
    public function send(string $from, string $to, string $subject, string $pathtemplate, array $parameters)
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($pathtemplate)
            ->context($parameters);

        $this->mailer->send($email);
    }
}
