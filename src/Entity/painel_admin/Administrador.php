<?php

namespace App\Entity\painel_admin;

use App\Repository\painel_admin\Administrador_Repository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: Administrador_Repository::class)]
#[ORM\Table(name: 'administrador')]
#[ORM\HasLifecycleCallbacks]
class Administrador implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password_hash = null;

    #[ORM\Column(length: 20, options: ["default" => "pendente"])]
    private ?string $status = 'pendente';

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $ultimo_login_em = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password_hash;
    }

    public function setPassword(string $password): static
    {
        $this->password_hash = $password;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUltimoLoginEm(): ?\DateTimeImmutable
    {
        return $this->ultimo_login_em;
    }

    public function setUltimoLoginEm(?\DateTimeImmutable $ultimo_login_em): static
    {
        $this->ultimo_login_em = $ultimo_login_em;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function registrarLogin(): void
    {
        $this->ultimo_login_em = new \DateTimeImmutable();
    }
}
