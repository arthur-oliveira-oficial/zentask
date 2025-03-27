<?php

namespace App\Repository\painel_admin;

use App\Entity\painel_admin\Empresa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Empresa>
 *
 * @method Empresa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Empresa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Empresa[]    findAll()
 * @method Empresa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Empresa_Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Empresa::class);
    }

    /**
     * Encontra uma empresa pelo nome
     */
    public function findByNome(string $nome): ?Empresa
    {
        return $this->findOneBy(['nome' => $nome]);
    }

    /**
     * Encontra empresas pelo status
     */
    public function findByStatus(string $status): array
    {
        return $this->findBy(['status' => $status]);
    }

    /**
     * Verifica se existe uma empresa com o nome informado
     */
    public function nomeExists(string $nome): bool
    {
        return null !== $this->findByNome($nome);
    }

    /**
     * Encontra empresas ativas
     */
    public function findAtivas(): array
    {
        return $this->findByStatus('ativo');
    }

    /**
     * Encontra empresas com plano expirado
     */
    public function findComPlanoExpirado(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.data_de_expiracao_plano IS NOT NULL')
            ->andWhere('e.data_de_expiracao_plano < :hoje')
            ->setParameter('hoje', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    /**
     * Encontra empresas por plano de pagamento
     */
    public function findByPlanoDePagamento(int $planoId): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.plano_de_pagamento = :planoId')
            ->setParameter('planoId', $planoId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Salva uma empresa no banco de dados
     */
    public function save(Empresa $empresa, bool $flush = true): void
    {
        $this->getEntityManager()->persist($empresa);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove uma empresa do banco de dados
     */
    public function remove(Empresa $empresa, bool $flush = true): void
    {
        $this->getEntityManager()->remove($empresa);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
