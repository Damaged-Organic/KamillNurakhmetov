<?php
// src/AppBundle/Security/Authorization/Voter/StoryVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter,
    Symfony\Component\Security\Core\User\AdvancedUserInterface;

class StoryVoter extends AbstractVoter
{
    const STORY_READER_SUBSCRIBED        = "STORY_READER_SUBSCRIBED";
    const STORY_READER_REALLY_SUBSCRIBED = "STORY_READER_REALLY_SUBSCRIBED";

    protected function getSupportedAttributes()
    {
        return [self::STORY_READER_SUBSCRIBED, self::STORY_READER_REALLY_SUBSCRIBED];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Story'];
    }

    protected function isGranted($attribute, $story, $user = NULL)
    {
        switch($attribute)
        {
            case self::STORY_READER_SUBSCRIBED:
                return $this->isSubscribed($story, $user);
            break;

            case self::STORY_READER_REALLY_SUBSCRIBED:
                return $this->isReallySubscribed($story, $user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    public function isSubscribed($story, $user = NULL)
    {
        if( $story->getIsFreeForAll() )
            return TRUE;

        if( !$user instanceof AdvancedUserInterface )
            return FALSE;

        if( $user->getIsSubscribed() )
            return TRUE;

        return FALSE;
    }

    public function isReallySubscribed($story, $user = NULL)
    {
        if( !$user instanceof AdvancedUserInterface )
            return FALSE;

        if( $story->getIsFreeForAll() ) {
            return TRUE;
        } else {
            return ( $user->getIsSubscribed() )
                ? TRUE
                : FALSE;
        }

        return FALSE;
    }
}
