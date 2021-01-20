<?php

namespace App\Notification;

use App\Entity\User;
use Twig\Environment;
use App\Entity\Contact;

class ContactNotification {

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer   = $mailer;
        $this->renderer = $renderer;
    }

    public function notify(User $user, Contact $contact)
    {
        $message = (new \Swift_Message('sujet : ' . $contact->getSubject()))
            ->setFrom($user->getEmail())
            ->setTo('admin@gmail.com')
            ->setBody(
                $this->renderer->render('email/contact.html.twig', [
                    'contact' => $contact,
                    'user' => $user
                ]), 
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}