<?php

namespace App\Entity\PainelAdmin;

use App\Repository\PainelAdmin\HistoricoDePagamentoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;
use DateTimeInterface;
use Stringable;

/**
 * Entidade que representa um registro de pagamento no sistema
 */
#[ORM\Entity(repositoryClass: HistoricoDePagamentoRepository::class)]
#[ORM\Table(name: 'historico_de_pagamentos')]
#[ORM\HasLifecycleCallbacks]
class HistoricoDePagamento implements Stringable
{
    /**
     * Status possíveis para um pagamento
     */
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_APROVADO = 'aprovado';
    public const STATUS_RECUSADO = 'recusado';
    public const STATUS_CANCELADO = 'cancelado';
    public const STATUS_REEMBOLSADO = 'reembolsado';

    /**
     * Métodos de pagamento suportados
     */
    public const METODO_CARTAO = 'cartao';
    public const METODO_BOLETO = 'boleto';
    public const METODO_PIX = 'pix';
    public const METODO_TRANSFERENCIA = 'transferencia';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historicoDePagamentos')]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: 'A empresa não pode estar vazia')]
    private ?Empresa $empresa = null;

    #[ORM\ManyToOne(targetEntity: PlanoDePagamento::class)]
    #[ORM\JoinColumn(name: 'plano_de_pagamento_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: 'O plano de pagamento não pode estar vazio')]
    private ?PlanoDePagamento $plano_de_pagamento = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'A data de pagamento não pode estar vazia')]
    private ?DateTimeInterface $data_pagamento = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'O valor pago não pode estar vazio')]
    #[Assert\Positive(message: 'O valor pago deve ser positivo')]
    private ?string $valor_pago = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'O método de pagamento não pode estar vazio')]
    #[Assert\Choice(
        choices: [self::METODO_CARTAO, self::METODO_BOLETO, self::METODO_PIX, self::METODO_TRANSFERENCIA],
        message: 'Método de pagamento inválido'
    )]
    private ?string $metodo_pagamento = null;

    #[ORM\Column(length: 20, options: ["default" => self::STATUS_PENDENTE])]
    #[Assert\Choice(
        choices: [self::STATUS_PENDENTE, self::STATUS_APROVADO, self::STATUS_RECUSADO, self::STATUS_CANCELADO, self::STATUS_REEMBOLSADO],
        message: 'Status de pagamento inválido'
    )]
    private ?string $status_pagamento = self::STATUS_PENDENTE;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transacao_id = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updated_at = null;

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

    public function getDataPagamento(): ?DateTimeInterface
    {
        return $this->data_pagamento;
    }

    public function setDataPagamento(DateTimeInterface $data_pagamento): static
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
        if (!in_array($metodo_pagamento, [
            self::METODO_CARTAO,
            self::METODO_BOLETO,
            self::METODO_PIX,
            self::METODO_TRANSFERENCIA
        ])) {
            throw new \InvalidArgumentException(sprintf(
                'Método de pagamento inválido. Métodos permitidos: %s',
                implode(', ', [
                    self::METODO_CARTAO,
                    self::METODO_BOLETO,
                    self::METODO_PIX,
                    self::METODO_TRANSFERENCIA
                ])
            ));
        }

        $this->metodo_pagamento = $metodo_pagamento;

        return $this;
    }

    public function getStatusPagamento(): ?string
    {
        return $this->status_pagamento;
    }

    public function setStatusPagamento(string $status_pagamento): static
    {
        if (!in_array($status_pagamento, [
            self::STATUS_PENDENTE,
            self::STATUS_APROVADO,
            self::STATUS_RECUSADO,
            self::STATUS_CANCELADO,
            self::STATUS_REEMBOLSADO
        ])) {
            throw new \InvalidArgumentException(sprintf(
                'Status de pagamento inválido. Status permitidos: %s',
                implode(', ', [
                    self::STATUS_PENDENTE,
                    self::STATUS_APROVADO,
                    self::STATUS_RECUSADO,
                    self::STATUS_CANCELADO,
                    self::STATUS_REEMBOLSADO
                ])
            ));
        }

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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * Verifica se o pagamento está pendente
     */
    public function isPendente(): bool
    {
        return $this->status_pagamento === self::STATUS_PENDENTE;
    }

    /**
     * Verifica se o pagamento foi aprovado
     */
    public function isAprovado(): bool
    {
        return $this->status_pagamento === self::STATUS_APROVADO;
    }

    /**
     * Verifica se o pagamento foi recusado
     */
    public function isRecusado(): bool
    {
        return $this->status_pagamento === self::STATUS_RECUSADO;
    }

    /**
     * Verifica se o pagamento foi cancelado
     */
    public function isCancelado(): bool
    {
        return $this->status_pagamento === self::STATUS_CANCELADO;
    }

    /**
     * Verifica se o pagamento foi reembolsado
     */
    public function isReembolsado(): bool
    {
        return $this->status_pagamento === self::STATUS_REEMBOLSADO;
    }

    /**
     * Aprova o pagamento
     */
    public function aprovar(): static
    {
        $this->status_pagamento = self::STATUS_APROVADO;

        return $this;
    }

    /**
     * Recusa o pagamento
     */
    public function recusar(): static
    {
        $this->status_pagamento = self::STATUS_RECUSADO;

        return $this;
    }

    /**
     * Cancela o pagamento
     */
    public function cancelar(): static
    {
        $this->status_pagamento = self::STATUS_CANCELADO;

        return $this;
    }

    /**
     * Reembolsa o pagamento
     */
    public function reembolsar(): static
    {
        $this->status_pagamento = self::STATUS_REEMBOLSADO;

        return $this;
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
     * Implementação da interface Stringable para exibir o pagamento como string
     */
    public function __toString(): string
    {
        $empresa = $this->empresa ? $this->empresa->getNome() : 'N/A';
        $plano = $this->plano_de_pagamento ? $this->plano_de_pagamento->getNome() : 'N/A';
        $data = $this->data_pagamento ? $this->data_pagamento->format('d/m/Y') : 'N/A';

        return sprintf(
            'Pagamento #%d: %s - %s (%s) - %s',
            $this->id ?? 0,
            $empresa,
            $plano,
            $data,
            $this->status_pagamento
        );
    }
}
