<?php

namespace App\Entity\PainelAdmin;

use App\Repository\PainelAdmin\EmpresaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTimeImmutable;
use DateTimeInterface;
use Stringable;

/**
 * Entidade que representa uma empresa no sistema
 */
#[ORM\Entity(repositoryClass: EmpresaRepository::class)]
#[ORM\Table(name: 'empresas')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['nome'], message: 'Esta empresa já está cadastrada')]
class Empresa implements Stringable
{
    /**
     * Status possíveis para uma empresa
     */
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_SUSPENSO = 'suspenso';
    public const STATUS_INATIVO = 'inativo';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'O nome da empresa não pode estar vazio')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'O nome da empresa deve ter pelo menos {{ limit }} caracteres',
        maxMessage: 'O nome da empresa não pode exceder {{ limit }} caracteres'
    )]
    private ?string $nome = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'O host do banco de dados não pode estar vazio')]
    private ?string $host_db = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'O usuário do banco de dados não pode estar vazio')]
    private ?string $usuario_db = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'A senha do banco de dados não pode estar vazia')]
    private ?string $senha_db = null;

    #[ORM\ManyToOne(targetEntity: PlanoDePagamento::class)]
    #[ORM\JoinColumn(name: 'plano_de_pagamento_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: 'Um plano de pagamento deve ser selecionado')]
    private ?PlanoDePagamento $plano_de_pagamento = null;

    #[ORM\Column(length: 20, options: ["default" => self::STATUS_PENDENTE])]
    #[Assert\Choice(
        choices: [self::STATUS_PENDENTE, self::STATUS_ATIVO, self::STATUS_SUSPENSO, self::STATUS_INATIVO],
        message: 'Status inválido'
    )]
    private ?string $status = self::STATUS_PENDENTE;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $data_de_expiracao_plano = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(
        mappedBy: 'empresa',
        targetEntity: HistoricoDePagamento::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $historicoDePagamentos;

    public function __construct()
    {
        $this->historicoDePagamentos = new ArrayCollection();
    }

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

    public function getHostDb(): ?string
    {
        return $this->host_db;
    }

    public function setHostDb(string $host_db): static
    {
        $this->host_db = trim($host_db);

        return $this;
    }

    public function getUsuarioDb(): ?string
    {
        return $this->usuario_db;
    }

    public function setUsuarioDb(string $usuario_db): static
    {
        $this->usuario_db = trim($usuario_db);

        return $this;
    }

    public function getSenhaDb(): ?string
    {
        return $this->senha_db;
    }

    public function setSenhaDb(string $senha_db): static
    {
        $this->senha_db = $senha_db;

        return $this;
    }

    public function getPlanoDePagamento(): ?PlanoDePagamento
    {
        return $this->plano_de_pagamento;
    }

    public function setPlanoDePagamento(?PlanoDePagamento $plano_de_pagamento): static
    {
        $this->plano_de_pagamento = $plano_de_pagamento;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!in_array($status, [self::STATUS_PENDENTE, self::STATUS_ATIVO, self::STATUS_SUSPENSO, self::STATUS_INATIVO])) {
            throw new \InvalidArgumentException('Status inválido');
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Verifica se a empresa está ativa
     */
    public function isAtiva(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    /**
     * Verifica se a empresa está pendente
     */
    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }

    /**
     * Verifica se a empresa está suspensa
     */
    public function isSuspensa(): bool
    {
        return $this->status === self::STATUS_SUSPENSO;
    }

    /**
     * Verifica se a empresa está inativa
     */
    public function isInativa(): bool
    {
        return $this->status === self::STATUS_INATIVO;
    }

    public function getDataDeExpiracaoPlano(): ?DateTimeInterface
    {
        return $this->data_de_expiracao_plano;
    }

    public function setDataDeExpiracaoPlano(?DateTimeInterface $data_de_expiracao_plano): static
    {
        $this->data_de_expiracao_plano = $data_de_expiracao_plano;

        return $this;
    }

    /**
     * Verifica se o plano da empresa está expirado
     */
    public function isPlanoExpirado(): bool
    {
        if (null === $this->data_de_expiracao_plano) {
            return false;
        }

        return $this->data_de_expiracao_plano < new \DateTime();
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * @return Collection<int, HistoricoDePagamento>
     */
    public function getHistoricoDePagamentos(): Collection
    {
        return $this->historicoDePagamentos;
    }

    public function addHistoricoDePagamento(HistoricoDePagamento $historicoDePagamento): static
    {
        if (!$this->historicoDePagamentos->contains($historicoDePagamento)) {
            $this->historicoDePagamentos->add($historicoDePagamento);
            $historicoDePagamento->setEmpresa($this);
        }

        return $this;
    }

    public function removeHistoricoDePagamento(HistoricoDePagamento $historicoDePagamento): static
    {
        if ($this->historicoDePagamentos->removeElement($historicoDePagamento)) {
            // set the owning side to null (unless already changed)
            if ($historicoDePagamento->getEmpresa() === $this) {
                $historicoDePagamento->setEmpresa(null);
            }
        }

        return $this;
    }

    /**
     * Obtém o último pagamento registrado para esta empresa
     */
    public function getUltimoPagamento(): ?HistoricoDePagamento
    {
        if ($this->historicoDePagamentos->isEmpty()) {
            return null;
        }

        $pagamentos = $this->historicoDePagamentos->toArray();
        usort($pagamentos, function (HistoricoDePagamento $a, HistoricoDePagamento $b) {
            return $b->getDataPagamento() <=> $a->getDataPagamento();
        });

        return $pagamentos[0] ?? null;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updated_at = new DateTimeImmutable();
    }

    /**
     * Implementação da interface Stringable para exibir a empresa como string
     */
    public function __toString(): string
    {
        return $this->nome ?? 'Nova Empresa';
    }
}
