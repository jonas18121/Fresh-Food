<?php

namespace App\Notification;

use App\Entity\User;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxeError;

class CreateAccountNotification {

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

    public function notifyCreateAccountForAdmin()
    {
        $message = (new \Swift_Message('Fresh Food - Nouvelle inscription '))
            ->setFrom('no-reply@freshfood.fr')
            ->setTo('admin@gmail.com')
            ->setBody( $this->renderer->render('email/createAccountForAdmin.html.twig'),'text/html' )
        ;
        $this->mailer->send($message);
    }

    public function notifyCreateAccountForUser(User $user)
    {
        $message = (new \Swift_Message('Fresh Food - inscription '))
            ->setFrom('no-reply@freshfood.fr')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderer->render('email/createAccountForUser.html.twig', [
                   'token' => $user->getActivationToken(),
                ]), 
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}