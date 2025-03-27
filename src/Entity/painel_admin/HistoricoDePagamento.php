<?php

namespace App\Entity\painel_admin;

use App\Repository\painel_admin\HistoricoDePagamento_Repository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoricoDePagamento_Repository::class)]
#[ORM\Table(name: 'historico_de_pagamentos')]
#[ORM\HasLifecycleCallbacks]
class HistoricoDePagamento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historicoDePagamentos')]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false)]
    private ?Empresa $empresa = null;

    #[ORM\ManyToOne(targetEntity: PlanoDePagamento::class)]
    #[ORM\JoinColumn(name: 'plano_de_pagamento_id', referencedColumnName: 'id', nullable: false)]
    private ?PlanoDePagamento $plano_de_pagamento = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $data_pagamento = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $valor_pago = null;

    #[ORM\Column(length: 50)]
    private ?string $metodo_pagamento = null;

    #[ORM\Column(length: 20, options: ["default" => "pendente"])]
    private ?string $status_pagamento = 'pendente';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transacao_id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?Empresa $empresa): static
    {
        $this->empresa = $empresa;

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

    public function getDataPagamento(): ?\DateTimeInterface
    {
        return $this->data_pagamento;
    }

    public function setDataPagamento(\DateTimeInterface $data_pagamento): static
    {
        $this->data_pagamento = $data_pagamento;

        return $this;
    }

    public function getValorPago(): ?string
    {
        return $this->valor_pago;
    }

    public function setValorPago(string $valor_pago): static
    {
        $this->valor_pago = $valor_pago;

        return $this;
    }

    public function getMetodoPagamento(): ?string
    {
        return $this->metodo_pagamento;
    }

    public function setMetodoPagamento(string $metodo_pagamento): static
    {
        $this->metodo_pagamento = $metodo_pagamento;

        return $this;
    }

    public function getStatusPagamento(): ?string
    {
        return $this->status_pagamento;
    }

    public function setStatusPagamento(string $status_pagamento): static
    {
        $this->status_pagamento = $status_pagamento;

        return $this;
    }

    public function getTransacaoId(): ?string
    {
        return $this->transacao_id;
    }

    public function setTransacaoId(?string $transacao_id): static
    {
        $this->transacao_id = $transacao_id;

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
