<?php

namespace App\Entity;

use App\Repository\EmpresaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpresaRepository::class)]
#[ORM\Table(name: 'empresas')]
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

    #[ORM\ManyToOne(inversedBy: 'empresas')]
    #[ORM\JoinColumn(name: 'plano_de_pagamento_id', referencedColumnName: 'id', nullable: false)]
    private ?PlanoDePagamento $planoDePagamento = null;

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
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
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

    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function getHostDb(): ?string
    {
        return $this->host_db;
    }

    public function setHostDb(string $host_db): self
    {
        $this->host_db = $host_db;
        return $this;
    }

    public function getUsuarioDb(): ?string
    {
        return $this->usuario_db;
    }

    public function setUsuarioDb(string $usuario_db): self
    {
        $this->usuario_db = $usuario_db;
        return $this;
    }

    public function getSenhaDb(): ?string
    {
        return $this->senha_db;
    }

    public function setSenhaDb(string $senha_db): self
    {
        $this->senha_db = $senha_db;
        return $this;
    }

    public function getPlanoDePagamento(): ?PlanoDePagamento
    {
        return $this->planoDePagamento;
    }

    public function setPlanoDePagamento(?PlanoDePagamento $planoDePagamento): self
    {
        $this->planoDePagamento = $planoDePagamento;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDataDeExpiracaoPlano(): ?\DateTimeInterface
    {
        return $this->data_de_expiracao_plano;
    }

    public function setDataDeExpiracaoPlano(?\DateTimeInterface $data_de_expiracao_plano): self
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

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return Collection<int, HistoricoDePagamento>
     */
    public function getHistoricoDePagamentos(): Collection
    {
        return $this->historicoDePagamentos;
    }
}
