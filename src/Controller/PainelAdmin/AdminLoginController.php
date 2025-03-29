<?php

namespace App\Controller\PainelAdmin;

use App\Entity\PainelAdmin\Administrador;
use App\Repository\PainelAdmin\AdministradorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/painel')]
final class AdminLoginController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdministradorRepository $administradorRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/login', name: 'admin_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->createErrorResponse(
                'Email e senha são obrigatórios',
                Response::HTTP_BAD_REQUEST
            );
        }

        $administrador = $this->administradorRepository->findOneBy(['email' => $data['email']]);

        if (!$administrador) {
            return $this->createErrorResponse(
                'Credenciais inválidas',
                Response::HTTP_UNAUTHORIZED
            );
        }

        if ($administrador->getStatus() !== 'ativo') {
            return $this->createErrorResponse(
                'Sua conta não está ativa. Entre em contato com o suporte.',
                Response::HTTP_FORBIDDEN
            );
        }

        if (!$this->passwordHasher->isPasswordValid($administrador, $data['password'])) {
            return $this->createErrorResponse(
                'Credenciais inválidas',
                Response::HTTP_UNAUTHORIZED
            );
        }

        $administrador->registrarLogin();
        $this->entityManager->flush();

        $token = $this->jwtManager->create($administrador);

        return $this->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    #[Route('/check-token', name: 'admin_check_token', methods: ['GET'])]
    public function checkToken(): JsonResponse
    {
        /** @var Administrador|null $administrador */
        $administrador = $this->getUser();

        if (!$administrador instanceof Administrador) {
            return $this->createErrorResponse(
                'Token inválido ou expirado',
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->json([
            'status' => 'success',
            'message' => 'Token válido',
            'user' => [
                'id' => $administrador->getId(),
                'nome' => $administrador->getNome(),
                'email' => $administrador->getEmail()
            ]
        ]);
    }

    private function createErrorResponse(string $message, int $statusCode): JsonResponse
    {
        return $this->json([
            'status' => 'error',
            'message' => $message
        ], $statusCode);
    }
}
