<?php

namespace App\Entity;

use App\Repository\PlanoDePagamentoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanoDePagamentoRepository::class)]
#[ORM\Table(name: 'planos_de_pagamento')]
class PlanoDePagamento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $nome = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descricao = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $preco = null;

    #[ORM\Column(length: 20)]
    private ?string $periodicidade = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $recursos = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'planoDePagamento', targetEntity: Empresa::class)]
    private Collection $empresas;

    #[ORM\OneToMany(mappedBy: 'planoDePagamento', targetEntity: HistoricoDePagamento::class)]
    private Collection $historicoDePagamentos;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
        $this->empresas = new ArrayCollection();
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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;
        return $this;
    }

    public function getPreco(): ?string
    {
        return $this->preco;
    }

    public function setPreco(string $preco): self
    {
        $this->preco = $preco;
        return $this;
    }

    public function getPeriodicidade(): ?string
    {
        return $this->periodicidade;
    }

    public function setPeriodicidade(string $periodicidade): self
    {
        $this->periodicidade = $periodicidade;
        return $this;
    }

    public function getRecursos(): ?string
    {
        return $this->recursos;
    }

    public function setRecursos(?string $recursos): self
    {
        $this->recursos = $recursos;
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
     * @return Collection<int, Empresa>
     */
    public function getEmpresas(): Collection
    {
        return $this->empresas;
    }

    /**
     * @return Collection<int, HistoricoDePagamento>
     */
    public function getHistoricoDePagamentos(): Collection
    {
        return $this->historicoDePagamentos;
    }
}
