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
     * @param array $destination
     * @param array $parameters
     * @return void
     */
    public function send(array $destination, array $parameters)
    {
        $email = (new TemplatedEmail())
            ->from($destination["from"])
            ->to($destination["to"])
            ->subject($destination["subject"])
            ->htmlTemplate($destination["template"])
            ->context($parameters);

        $this->mailer->send($email);
    }
}
