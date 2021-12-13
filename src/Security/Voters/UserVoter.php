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
        
        $userLogeado = $token->getUser();
        
        if(!$userLogeado instanceof User)
            return false;

        $method = 'can'.ucfirst($attribute);

        return $this->$method( $userLogeado, $subject );
    }

    private function canShow ( User $userLogeado ): bool {

        if (in_array('ROLE_SUPERVISOR', $userLogeado->getRoles(), true) || in_array('ROLE_ADMIN', $userLogeado->getRoles(), true))
            return true;
    
        return false;

    }

    private function canCreate( User $userLogeado ): bool {
        
        if (in_array('ROLE_ADMIN', $userLogeado->getRoles(), true))
            return true;
        
        return false;

    }
    private function canEdit(  User $userLogeado, ?User $user ): bool {
    
        // return $this->security->getuser() ==  $user ;
        if (in_array('ROLE_ADMIN', $userLogeado->getRoles(), true))
            return true;
        // dd( $user );
            // $this->security->isGranted('ROLE_EDITOR');
        if( $userLogeado ===  $user) return 'hello world';

        return false;
    }

    // private function canEdit(  User $user ): bool {
    
    //     // return $this->security->getuser() ==  $user ;
    //     if (in_array('ROLE_ADMIN', $user->getRoles(), true))
    //         return true;
    //     // dd( $user );
    //         // $this->security->isGranted('ROLE_EDITOR');
    //     if( $this->security->getuser() ===  $user )
    //         return true;

    //     return false;
    // }

    private function canDelete( User $userLogeado, ?User $userAComprobar ): bool {
        
        return $this->canEdit( $userLogeado, $userAComprobar );
    }
}