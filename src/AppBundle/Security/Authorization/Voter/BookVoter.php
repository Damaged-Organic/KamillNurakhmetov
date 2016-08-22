<?php
// src/AppBundle/Security/Authorization/Voter/BookVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter,
    Symfony\Component\Security\Core\User\AdvancedUserInterface;

class BookVoter extends AbstractVoter
{
    const BOOK_ACQUIRED = 'BOOK_ACQUIRED';

    protected function getSupportedAttributes()
    {
        return [self::BOOK_ACQUIRED];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Book'];
    }

    protected function isGranted($attribute, $book, $user = NULL)
    {
        if( !$user instanceof AdvancedUserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::BOOK_ACQUIRED:
                return $this->isAcquired($book, $user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    protected function isAcquired($book, $user)
    {
        if( !in_array('ROLE_READER', $user->getRoles()) )
            return FALSE;

        if( $user->getBooks()->contains($book) )
            return TRUE;

        return FALSE;
    }
}