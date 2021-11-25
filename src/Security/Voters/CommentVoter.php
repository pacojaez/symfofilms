<?php 
namespace App\Security\Voters;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class CommentVoter extends Voter {

    private $security, $operaciones;


    public function __construct( Security $security ){
        $this->security = $security;
        $this->operaciones = ['create', 'edit', 'delete'];
    }

    protected function supports(string $attribute, $subject): bool {

        if( !in_array($attribute, $this->operaciones))
            return false;
        
        if( !$subject instanceof Comment )
        return false;

        return true;
    }

    protected function voteOnAttribute( string $attribute, $subject , TokenInterface $token ) {
        
        $user = $token->getUser();
        
        if(!$user instanceof User)
            return false;

        $method = 'can'.ucfirst($attribute);

        return $this->$method($subject, $user);
    }

    private function isOwner( Comment $comment, ?User $user ): bool {
       
        return $user->getId() === $comment->getUser()->getId();
    }

    private function canCreate(Comment $comment, ?User $user ): bool {
        return true;
    }

    private function canEdit( Comment $comment, ?User $user ): bool {
       
        return $this->isOwner( $comment, $user );
    }

    private function canDelete( Comment $comment, ?User $user ): bool {
       
        return $this->canEdit( $comment, $user );
    }
}