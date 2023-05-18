<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Exception;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public $userRepository;

    public function __construct(ManagerRegistry $registry)
    {
        $this->userRepository = new UserRepository($registry);
    }

    function findUser() {
        $users = $this->userRepository->findBy([]);
        $serializedUsers = [];

        foreach ($users as $user) {
            $serializedUsers[] = [
                'id' => $user->getId(),
                'lastname' => $user->getLastname(),
                'firstname' => $user->getFirstname(),
                'status' => $user->getStatus(),
            ];
        }
        return $serializedUsers;
    }

    function createUser(Request $request){
        $lastname = $request->request->get('lastname');
        $firstname = $request->request->get('firstname');

        if(empty($lastname) || empty($firstname)) throw new Exception("L'un des paramètres est null");

        $user = new User($lastname, $firstname);
        
        $userCreate = $this->userRepository->save($user, true);
        if (isset($userCreate['error'])) {
            throw new Exception($userCreate['error']);
        }
    
        return $userCreate;
    }

    function updateUserStatus(int $id): void {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $status = 0;

        if($user->getStatus() == 1) $status = 0;
        else $status = 1;

        $user->setStatus($status);
        $this->userRepository->save($user, true);
    }
}