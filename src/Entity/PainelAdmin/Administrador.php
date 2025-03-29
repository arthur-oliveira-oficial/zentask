<?php

namespace App\Entity\PainelAdmin;

use App\Repository\PainelAdmin\AdministradorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Entidade que representa um administrador do sistema
 */
#[ORM\Entity(repositoryClass: AdministradorRepository::class)]
#[ORM\Table(name: 'administrador')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Este email já está em uso')]
class Administrador implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Status possíveis para um administrador
     */
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_BLOQUEADO = 'bloqueado';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'O nome não pode estar vazio')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'O nome deve ter pelo menos {{ limit }} caracteres', maxMessage: 'O nome não pode exceder {{ limit }} caracteres')]
    private ?string $nome = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'O email não pode estar vazio')]
    #[Assert\Email(message: 'O email {{ value }} não é um email válido')]
    private ?string $email = null;

    #[ORM\Column(length: 255, name: 'password_hash')]
    private ?string $password = null;

    #[ORM\Column(length: 20, options: ["default" => self::STATUS_PENDENTE])]
    private ?string $status = self::STATUS_PENDENTE;

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
        $this->nome = trim($nome);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = strtolower(trim($email));

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Define o status do administrador
     */
    public function setStatus(string $status): static
    {
        if (!in_array($status, [self::STATUS_PENDENTE, self::STATUS_ATIVO, self::STATUS_BLOQUEADO])) {
            throw new \InvalidArgumentException(sprintf(
                'Status inválido. Status permitidos: %s',
                implode(', ', [self::STATUS_PENDENTE, self::STATUS_ATIVO, self::STATUS_BLOQUEADO])
            ));
        }

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

    /**
     * Registra que o administrador fez login
     */
    public function registrarLogin(): static
    {
        $this->ultimo_login_em = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Verifica se o administrador está ativo
     */
    public function isAtivo(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    /**
     * Verifica se o administrador está pendente
     */
    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }

    /**
     * Verifica se o administrador está bloqueado
     */
    public function isBloqueado(): bool
    {
        return $this->status === self::STATUS_BLOQUEADO;
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

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function eraseCredentials(): void
    {
        // Método intencionalmente vazio - não armazenamos credenciais temporárias nesta classe
    }
}
