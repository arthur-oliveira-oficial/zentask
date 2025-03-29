<?php

namespace App\DataFixtures;

use App\Entity\PainelAdmin\Administrador;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsuarioAdminFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Administrador();
        $admin->setNome('admin');
        $admin->setEmail('admin@teste.com');

        // Hash da senha
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'admin12345'
        );
        $admin->setPassword($hashedPassword);

        // Definir status como ativo em vez do padrão "pendente"
        $admin->setStatus('ativo');

        // DateTimes são definidos automaticamente pelo lifecycle callback prePersist

        $manager->persist($admin);
        $manager->flush();
    }
}
