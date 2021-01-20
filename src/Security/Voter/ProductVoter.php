<?php

namespace App\Security\Voter;

use App\Entity\Product;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProductVoter extends Voter
{
    const SHOW      = 'show';
    const EDIT      = 'edit';
    const DELETE    = 'delete';

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::SHOW, self::EDIT, self::DELETE])
            && $subject instanceof Product;
    }

    protected function voteOnAttribute($attribute, $product, TokenInterface $token)
    {
        return $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if(null == $product->getAuthor()){
            return false;
        }

        switch ($attribute) {
            case self::SHOW:
                return $product->getAuthor() === $user;
                break;
            case self::EDIT:
                return $product->getAuthor() === $user;
                break;
            case self::DELETE:
                return $product->getAuthor() === $user;
                break;
        }
    }
}
