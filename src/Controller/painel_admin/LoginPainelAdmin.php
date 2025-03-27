<?php

namespace App\Controller\painel_admin;

use App\Entity\painel_admin\Administrador;
use App\Repository\painel_admin\Administrador_Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/painel')]
class LoginPainelAdmin extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Administrador_Repository $adminRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        Administrador_Repository $adminRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->adminRepository = $adminRepository;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
        $this->validator = $validator;
    }

    #[Route('/login', name: 'admin_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validar os dados recebidos
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Email e senha são obrigatórios'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Buscar o administrador pelo email
        $administrador = $this->adminRepository->findOneBy(['email' => $data['email']]);

        // Verificar se o administrador existe
        if (!$administrador) {
            return $this->json([
                'status' => 'error',
                'message' => 'Credenciais inválidas'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Verificar se o administrador está ativo
        if ($administrador->getStatus() !== 'ativo') {
            return $this->json([
                'status' => 'error',
                'message' => 'Sua conta não está ativa. Entre em contato com o suporte.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Verificar a senha
        if (!$this->passwordHasher->isPasswordValid($administrador, $data['password'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Credenciais inválidas'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Registrar o login
        $administrador->registrarLogin();
        $this->entityManager->flush();

        // Gerar o token JWT
        $token = $this->jwtManager->create($administrador);

        // Retornar apenas o token
        return $this->json($token);
    }

    #[Route('/check-token', name: 'admin_check_token', methods: ['GET'])]
    public function checkToken(): JsonResponse
    {
        // Este endpoint verificará automaticamente o token via firewall JWT
        /** @var Administrador $administrador */
        $administrador = $this->getUser();

        if (!$administrador) {
            return $this->json([
                'status' => 'error',
                'message' => 'Token inválido ou expirado'
            ], Response::HTTP_UNAUTHORIZED);
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
}
