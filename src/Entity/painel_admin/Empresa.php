<?php

namespace App\Entity\painel_admin;

use App\Repository\painel_admin\Empresa_Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Empresa_Repository::class)]
#[ORM\Table(name: 'empresas')]
#[ORM\HasLifecycleCallbacks]
class Empresa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(length: 255)]
    private ?string $host_db = null;

    #[ORM\Column(length: 255)]
    private ?string $usuario_db = null;

    #[ORM\Column(length: 255)]
    private ?string $senha_db = null;

    #[ORM\ManyToOne(targetEntity: PlanoDePagamento::class)]
    #[ORM\JoinColumn(name: 'plano_de_pagamento_id', referencedColumnName: 'id', nullable: false)]
    private ?PlanoDePagamento $plano_de_pagamento = null;

    #[ORM\Column(length: 20, options: ["default" => "pendente"])]
    private ?string $status = 'pendente';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $data_de_expiracao_plano = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'empresa', targetEntity: HistoricoDePagamento::class)]
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
        $this->nome = $nome;

        return $this;
    }

    public function getHostDb(): ?string
    {
        return $this->host_db;
    }

    public function setHostDb(string $host_db): static
    {
        $this->host_db = $host_db;

        return $this;
    }

    public function getUsuarioDb(): ?string
    {
        return $this->usuario_db;
    }

    public function setUsuarioDb(string $usuario_db): static
    {
        $this->usuario_db = $usuario_db;

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
        $this->status = $status;

        return $this;
    }

    public function getDataDeExpiracaoPlano(): ?\DateTimeInterface
    {
        return $this->data_de_expiracao_plano;
    }

    public function setDataDeExpiracaoPlano(?\DateTimeInterface $data_de_expiracao_plano): static
    {
        $this->data_de_expiracao_plano = $data_de_expiracao_plano;

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
}
