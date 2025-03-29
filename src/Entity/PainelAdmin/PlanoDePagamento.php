<?php

namespace App\Entity\PainelAdmin;

use App\Repository\PainelAdmin\PlanoDePagamentoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTimeImmutable;
use Stringable;

/**
 * Entidade que representa um plano de pagamento no sistema
 */
#[ORM\Entity(repositoryClass: PlanoDePagamentoRepository::class)]
#[ORM\Table(name: 'planos_de_pagamento')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['nome'], message: 'Este nome de plano já está em uso')]
class PlanoDePagamento implements Stringable
{
    /**
     * Periodicidades possíveis para um plano
     */
    public const PERIODICIDADE_MENSAL = 'mensal';
    public const PERIODICIDADE_TRIMESTRAL = 'trimestral';
    public const PERIODICIDADE_SEMESTRAL = 'semestral';
    public const PERIODICIDADE_ANUAL = 'anual';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'O nome do plano não pode estar vazio')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'O nome do plano deve ter pelo menos {{ limit }} caracteres',
        maxMessage: 'O nome do plano não pode exceder {{ limit }} caracteres'
    )]
    private ?string $nome = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descricao = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'O preço não pode estar vazio')]
    #[Assert\Positive(message: 'O preço deve ser um valor positivo')]
    private ?string $preco = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'A periodicidade não pode estar vazia')]
    #[Assert\Choice(
        choices: [self::PERIODICIDADE_MENSAL, self::PERIODICIDADE_TRIMESTRAL, self::PERIODICIDADE_SEMESTRAL, self::PERIODICIDADE_ANUAL],
        message: 'Periodicidade inválida'
    )]
    private ?string $periodicidade = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $recursos = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updated_at = null;

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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): static
    {
        $this->descricao = $descricao ? trim($descricao) : null;

        return $this;
    }

    public function getPreco(): ?string
    {
        return $this->preco;
    }

    /**
     * Obtém o preço formatado como moeda brasileira
     */
    public function getPrecoFormatado(): string
    {
        return 'R$ ' . number_format((float) $this->preco, 2, ',', '.');
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

    /**
     * Obtém a descrição da periodicidade formatada
     */
    public function getPeriodicidadeFormatada(): string
    {
        $formatos = [
            self::PERIODICIDADE_MENSAL => 'Mensal',
            self::PERIODICIDADE_TRIMESTRAL => 'Trimestral',
            self::PERIODICIDADE_SEMESTRAL => 'Semestral',
            self::PERIODICIDADE_ANUAL => 'Anual',
        ];

        return $formatos[$this->periodicidade] ?? $this->periodicidade;
    }

    public function setPeriodicidade(string $periodicidade): static
    {
        if (!in_array($periodicidade, [
            self::PERIODICIDADE_MENSAL,
            self::PERIODICIDADE_TRIMESTRAL,
            self::PERIODICIDADE_SEMESTRAL,
            self::PERIODICIDADE_ANUAL
        ])) {
            throw new \InvalidArgumentException(sprintf(
                'Periodicidade inválida. Periodicidades permitidas: %s',
                implode(', ', [
                    self::PERIODICIDADE_MENSAL,
                    self::PERIODICIDADE_TRIMESTRAL,
                    self::PERIODICIDADE_SEMESTRAL,
                    self::PERIODICIDADE_ANUAL
                ])
            ));
        }

        $this->periodicidade = $periodicidade;

        return $this;
    }

    public function getRecursos(): ?string
    {
        return $this->recursos;
    }

    /**
     * Obtém os recursos como um array de strings
     */
    public function getRecursosArray(): array
    {
        if (empty($this->recursos)) {
            return [];
        }

        return array_map('trim', explode("\n", $this->recursos));
    }

    public function setRecursos(?string $recursos): static
    {
        $this->recursos = $recursos;

        return $this;
    }

    /**
     * Adiciona um novo recurso à lista de recursos
     */
    public function adicionarRecurso(string $recurso): static
    {
        $recursos = $this->getRecursosArray();
        $recursos[] = trim($recurso);
        $this->recursos = implode("\n", $recursos);

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
     * Calcula o preço para um número específico de meses
     */
    public function calcularPrecoParaPeriodo(int $meses): float
    {
        $precoBase = (float) $this->preco;
        $mesesPorPeriodicidade = [
            self::PERIODICIDADE_MENSAL => 1,
            self::PERIODICIDADE_TRIMESTRAL => 3,
            self::PERIODICIDADE_SEMESTRAL => 6,
            self::PERIODICIDADE_ANUAL => 12,
        ];

        $precoMensal = $precoBase / ($mesesPorPeriodicidade[$this->periodicidade] ?? 1);
        return $precoMensal * $meses;
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
     * Implementação da interface Stringable para exibir o plano como string
     */
    public function __toString(): string
    {
        return $this->nome ?? 'Novo Plano';
    }
}
