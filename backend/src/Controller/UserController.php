<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

final class UserController extends AbstractController
{

    private UserService $userService;

    function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    #[Route('/user', name: 'user', methods: ['POST'])]
    #[OA\Post(
        path: '/user',
        summary: "Création d'un utilisateur",
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password', 'name', 'firstName'],
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                new OA\Property(property: 'password', type: 'string', example: 'SecurePass123'),
                new OA\Property(property: 'name', type: 'string', example: 'Doe'),
                new OA\Property(property: 'firstName', type: 'string', example: 'John'),
                new OA\Property(property: 'phoneNumber', type: 'string', example: '+330684682751'),
                new OA\Property(property: 'birthdate', type: 'date', example: '01/01/2001'),
            ]
        )
    )]
    public function register(Request $request) : JsonResponse{
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return new JsonResponse([
                'message' => 'JSON invalide',
            ], 400);
        }

        try {
            $this->userService->register($data);

            return new JsonResponse([
                'message' => 'Utilisateur créé avec succès',
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création de l\'utilisateur : ' . $e->getMessage(),
            ], 500);
        }
    }
}
