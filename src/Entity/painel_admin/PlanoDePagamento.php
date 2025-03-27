<?php

namespace App\Entity\painel_admin;

use App\Repository\painel_admin\PlanoDePagamento_Repository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanoDePagamento_Repository::class)]
#[ORM\Table(name: 'planos_de_pagamento')]
#[ORM\HasLifecycleCallbacks]
class PlanoDePagamento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $nome = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descricao = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $preco = null;

    #[ORM\Column(length: 20)]
    private ?string $periodicidade = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $recursos = null;

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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getPreco(): ?string
    {
        return $this->preco;
    }

    public function setPreco(string $preco): static
    {
        $this->preco = $preco;

        return $this;
    }

    public function getPeriodicidade(): ?string
    {
        return $this->periodicidade;
    }

    public function setPeriodicidade(string $periodicidade): static
    {
        $this->periodicidade = $periodicidade;

        return $this;
    }

    public function getRecursos(): ?string
    {
        return $this->recursos;
    }

    public function setRecursos(?string $recursos): static
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
