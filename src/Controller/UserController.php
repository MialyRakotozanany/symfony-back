<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\UserService;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{

    private $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    #[Route('/user', name: 'user')]
    public function index(): Response
    {
        $users = $this->userService->findUser();
        return $this->render('user.html.twig', ['users' => $users]);
    }

    #[Route('/user/list', name: 'user_list')]
    public function list(): JsonResponse
    {
        $users = $this->userService->findUser();
        return $this->json($users);
    }

    #[Route('/user/new', name: 'new_user')]
    public function createUser(Request $request): Response
    {
        $userCreate = "";
        $users = $this->userService->findUser();
        try{
            $userCreate = $this->userService->createUser($request);
            return $this->redirectToRoute('user', ['users' => $users]);
            
        }catch(Exception $e){
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('user');
        }
    }

    #[Route('/user/{id}', methods: ['PUT'])]
    public function changeStatus(int $id): JsonResponse
    {
        $this->userService->updateUserStatus($id);
        return new JsonResponse(['success' => 'Status changé avec succès'], Response::HTTP_OK);
    }
}
