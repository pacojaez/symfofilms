<?php 
namespace App\Security\Voters;

use App\Entity\Movie;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class MovieVoter extends Voter {

    private $security, $operaciones;


    public function __construct( Security $security ){
        $this->security = $security;
        $this->operaciones = ['create', 'edit', 'delete'];
    }

    protected function supports(string $attribute, $subject): bool {

        if( !in_array($attribute, $this->operaciones))
            return false;
        
        if( !$subject instanceof Movie )
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

    private function isOwner( Movie $movie, ?User $user ): bool {
       
       return $user === $movie->getUser();
    }

    private function canCreate(Movie $movie, ?User $user ): bool {
       
        return true;
    }

    private function canEdit( Movie $movie, ?User $user ): bool {
       
        return $user === $movie->getuser() || $this->security->isGranted('ROLE_EDITOR');
    }

    private function canDelete( Movie $movie, ?User $user ): bool {
       
        return $this->canEdit( $movie, $user );
    }
}