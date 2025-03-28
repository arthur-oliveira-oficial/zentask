<?php

declare(strict_types=1);

namespace App\Repository\PainelAdmin;

use App\Entity\PainelAdmin\HistoricoDePagamento;
use App\Entity\PainelAdmin\Empresa;
use App\Entity\PainelAdmin\PlanoDePagamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositório para gerenciar operações relacionadas aos pagamentos no banco de dados.
 *
 * @extends ServiceEntityRepository<HistoricoDePagamento>
 *
 * @method HistoricoDePagamento|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoricoDePagamento|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoricoDePagamento[]    findAll()
 * @method HistoricoDePagamento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoricoDePagamentoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoricoDePagamento::class);
    }

    /**
     * Encontra pagamentos por empresa.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findByEmpresa(Empresa $empresa): array
    {
        return $this->findBy(['empresa' => $empresa], ['data_pagamento' => 'DESC']);
    }

    /**
     * Encontra pagamentos por plano.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findByPlanoDePagamento(PlanoDePagamento $plano): array
    {
        return $this->findBy(['plano_de_pagamento' => $plano], ['data_pagamento' => 'DESC']);
    }

    /**
     * Encontra pagamentos por status.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findByStatusPagamento(string $status): array
    {
        return $this->findBy(['status_pagamento' => $status], ['data_pagamento' => 'DESC']);
    }

    /**
     * Encontra pagamentos por período.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findByPeriodo(\DateTimeInterface $dataInicio, \DateTimeInterface $dataFim): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.data_pagamento >= :dataInicio')
            ->andWhere('h.data_pagamento <= :dataFim')
            ->setParameter('dataInicio', $dataInicio)
            ->setParameter('dataFim', $dataFim)
            ->orderBy('h.data_pagamento', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encontra pagamentos por método de pagamento.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findByMetodoPagamento(string $metodo): array
    {
        return $this->findBy(['metodo_pagamento' => $metodo], ['data_pagamento' => 'DESC']);
    }

    /**
     * Encontra o último pagamento aprovado de uma empresa.
     */
    public function findUltimoPagamentoEmpresa(Empresa $empresa): ?HistoricoDePagamento
    {
        return $this->createQueryBuilder('h')
            ->where('h.empresa = :empresa')
            ->andWhere('h.status_pagamento = :status')
            ->setParameter('empresa', $empresa)
            ->setParameter('status', HistoricoDePagamento::STATUS_APROVADO)
            ->orderBy('h.data_pagamento', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Encontra pagamento por ID de transação.
     */
    public function findByTransacaoId(string $transacaoId): ?HistoricoDePagamento
    {
        return $this->findOneBy(['transacao_id' => $transacaoId]);
    }

    /**
     * Encontra pagamentos pendentes.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findPendentes(): array
    {
        return $this->findByStatusPagamento(HistoricoDePagamento::STATUS_PENDENTE);
    }

    /**
     * Encontra pagamentos aprovados.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findAprovados(): array
    {
        return $this->findByStatusPagamento(HistoricoDePagamento::STATUS_APROVADO);
    }

    /**
     * Encontra pagamentos recusados.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findRecusados(): array
    {
        return $this->findByStatusPagamento(HistoricoDePagamento::STATUS_RECUSADO);
    }

    /**
     * Encontra pagamentos cancelados.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findCancelados(): array
    {
        return $this->findByStatusPagamento(HistoricoDePagamento::STATUS_CANCELADO);
    }

    /**
     * Encontra pagamentos reembolsados.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findReembolsados(): array
    {
        return $this->findByStatusPagamento(HistoricoDePagamento::STATUS_REEMBOLSADO);
    }

    /**
     * Encontra pagamentos por faixa de valor.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findByFaixaValor(float $valorMinimo, float $valorMaximo): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.valor_pago >= :valorMinimo')
            ->andWhere('h.valor_pago <= :valorMaximo')
            ->setParameter('valorMinimo', $valorMinimo)
            ->setParameter('valorMaximo', $valorMaximo)
            ->orderBy('h.valor_pago', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encontra os pagamentos mais recentes, com limite opcional.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findMaisRecentes(int $limite = 10): array
    {
        return $this->createQueryBuilder('h')
            ->orderBy('h.data_pagamento', 'DESC')
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult();
    }

    /**
     * Encontra pagamentos para um determinado mês e ano.
     *
     * @return array<int, HistoricoDePagamento>
     */
    public function findByMesAno(int $mes, int $ano): array
    {
        $dataInicio = new \DateTime("$ano-$mes-01");
        $dataFim = clone $dataInicio;
        $dataFim->modify('last day of this month');

        return $this->findByPeriodo($dataInicio, $dataFim);
    }

    /**
     * Retorna um QueryBuilder base para consultas personalizadas.
     */
    public function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('h')
            ->orderBy('h.data_pagamento', 'DESC');
    }

    /**
     * Calcula o total de pagamentos aprovados para uma empresa.
     */
    public function calcularTotalPagamentosAprovados(Empresa $empresa): float
    {
        $result = $this->createQueryBuilder('h')
            ->select('SUM(h.valor_pago) as total')
            ->where('h.empresa = :empresa')
            ->andWhere('h.status_pagamento = :status')
            ->setParameter('empresa', $empresa)
            ->setParameter('status', HistoricoDePagamento::STATUS_APROVADO)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Salva um histórico de pagamento no banco de dados.
     */
    public function save(HistoricoDePagamento $historicoDePagamento, bool $flush = true): void
    {
        $this->getEntityManager()->persist($historicoDePagamento);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove um histórico de pagamento do banco de dados.
     */
    public function remove(HistoricoDePagamento $historicoDePagamento, bool $flush = true): void
    {
        $this->getEntityManager()->remove($historicoDePagamento);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
