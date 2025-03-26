<?php

namespace App\Repository\painel_admin;

use App\Entity\painel_admin\HistoricoDePagamento;
use App\Entity\painel_admin\Empresa;
use App\Entity\painel_admin\PlanoDePagamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoricoDePagamento>
 *
 * @method HistoricoDePagamento|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoricoDePagamento|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoricoDePagamento[]    findAll()
 * @method HistoricoDePagamento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoricoDePagamento_Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoricoDePagamento::class);
    }

    /**
     * Encontra pagamentos por empresa
     */
    public function findByEmpresa(Empresa $empresa): array
    {
        return $this->findBy(['empresa' => $empresa], ['data_pagamento' => 'DESC']);
    }

    /**
     * Encontra pagamentos por plano
     */
    public function findByPlanoDePagamento(PlanoDePagamento $plano): array
    {
        return $this->findBy(['plano_de_pagamento' => $plano], ['data_pagamento' => 'DESC']);
    }

    /**
     * Encontra pagamentos por status
     */
    public function findByStatusPagamento(string $status): array
    {
        return $this->findBy(['status_pagamento' => $status], ['data_pagamento' => 'DESC']);
    }

    /**
     * Encontra pagamentos por período
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
     * Encontra pagamentos por método de pagamento
     */
    public function findByMetodoPagamento(string $metodo): array
    {
        return $this->findBy(['metodo_pagamento' => $metodo], ['data_pagamento' => 'DESC']);
    }

    /**
     * Encontra o último pagamento de uma empresa
     */
    public function findUltimoPagamentoEmpresa(Empresa $empresa): ?HistoricoDePagamento
    {
        return $this->createQueryBuilder('h')
            ->where('h.empresa = :empresa')
            ->andWhere('h.status_pagamento = :status')
            ->setParameter('empresa', $empresa)
            ->setParameter('status', 'confirmado')
            ->orderBy('h.data_pagamento', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Encontra pagamentos com transação específica
     */
    public function findByTransacaoId(string $transacaoId): ?HistoricoDePagamento
    {
        return $this->findOneBy(['transacao_id' => $transacaoId]);
    }

    /**
     * Encontra pagamentos pendentes
     */
    public function findPendentes(): array
    {
        return $this->findByStatusPagamento('pendente');
    }

    /**
     * Encontra pagamentos confirmados
     */
    public function findConfirmados(): array
    {
        return $this->findByStatusPagamento('confirmado');
    }

    /**
     * Encontra pagamentos cancelados
     */
    public function findCancelados(): array
    {
        return $this->findByStatusPagamento('cancelado');
    }

    /**
     * Salva um histórico de pagamento no banco de dados
     */
    public function save(HistoricoDePagamento $historicoDePagamento, bool $flush = true): void
    {
        $this->getEntityManager()->persist($historicoDePagamento);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove um histórico de pagamento do banco de dados
     */
    public function remove(HistoricoDePagamento $historicoDePagamento, bool $flush = true): void
    {
        $this->getEntityManager()->remove($historicoDePagamento);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
