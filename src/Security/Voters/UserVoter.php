<?php 
namespace App\Security\Voters;

use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter {

    private $security, $operaciones;


    public function __construct( Security $security ){
        $this->security = $security;
        $this->operaciones = ['create', 'edit', 'delete', 'show'];
    }

    protected function supports(string $attribute, $subject): bool {

        if( !in_array($attribute, $this->operaciones))
            return false;
        
        if( !$subject instanceof User )
        return false;

        return true;
    }

    protected function voteOnAttribute( string $attribute, $subject , TokenInterface $token ) {
        
        $user = $token->getUser();
        
        if(!$user instanceof User)
            return false;

        $method = 'can'.ucfirst($attribute);

        return $this->$method( $user );
    }

    private function canShow ( User $user ): bool {
       
        return $user === $this->security->isGranted('ROLE_SUPERVISOR');

    }

    private function canCreate( User $user ): bool {
        
        if (in_array('ROLE_ADMIN', $user->getRoles(), true))
            return true;
        
        return false;

    }

    private function canEdit(  User $user ): bool {
       
        return $this->security->getuser() ==  $user ;
    }

    private function canDelete( User $user ): bool {
        
        return $this->canEdit( $user );
    }
}