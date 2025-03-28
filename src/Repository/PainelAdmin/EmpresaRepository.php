<?php

declare(strict_types=1);

namespace App\Repository\PainelAdmin;

use App\Entity\PainelAdmin\Empresa;
use App\Entity\PainelAdmin\PlanoDePagamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositório para gerenciar operações relacionadas às empresas no banco de dados.
 *
 * @extends ServiceEntityRepository<Empresa>
 *
 * @method Empresa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Empresa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Empresa[]    findAll()
 * @method Empresa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpresaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Empresa::class);
    }

    /**
     * Encontra uma empresa pelo nome.
     */
    public function findByNome(string $nome): ?Empresa
    {
        return $this->findOneBy(['nome' => trim($nome)]);
    }

    /**
     * Encontra empresas pelo status.
     *
     * @return array<int, Empresa>
     */
    public function findByStatus(string $status): array
    {
        return $this->findBy(['status' => $status], ['nome' => 'ASC']);
    }

    /**
     * Verifica se existe uma empresa com o nome informado.
     */
    public function nomeExists(string $nome): bool
    {
        return null !== $this->findByNome(trim($nome));
    }

    /**
     * Encontra empresas ativas.
     *
     * @return array<int, Empresa>
     */
    public function findAtivas(): array
    {
        return $this->findByStatus(Empresa::STATUS_ATIVO);
    }

    /**
     * Encontra empresas pendentes.
     *
     * @return array<int, Empresa>
     */
    public function findPendentes(): array
    {
        return $this->findByStatus(Empresa::STATUS_PENDENTE);
    }

    /**
     * Encontra empresas suspensas.
     *
     * @return array<int, Empresa>
     */
    public function findSuspensas(): array
    {
        return $this->findByStatus(Empresa::STATUS_SUSPENSO);
    }

    /**
     * Encontra empresas inativas.
     *
     * @return array<int, Empresa>
     */
    public function findInativas(): array
    {
        return $this->findByStatus(Empresa::STATUS_INATIVO);
    }

    /**
     * Encontra empresas com plano expirado.
     *
     * @return array<int, Empresa>
     */
    public function findComPlanoExpirado(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.data_de_expiracao_plano IS NOT NULL')
            ->andWhere('e.data_de_expiracao_plano < :hoje')
            ->setParameter('hoje', new \DateTime())
            ->orderBy('e.data_de_expiracao_plano', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encontra empresas que expirarão em X dias.
     *
     * @return array<int, Empresa>
     */
    public function findComPlanoProximoDeExpirar(int $dias = 7): array
    {
        $hoje = new \DateTime();
        $limite = (new \DateTime())->modify("+{$dias} days");

        return $this->createQueryBuilder('e')
            ->where('e.data_de_expiracao_plano IS NOT NULL')
            ->andWhere('e.data_de_expiracao_plano >= :hoje')
            ->andWhere('e.data_de_expiracao_plano <= :limite')
            ->setParameter('hoje', $hoje)
            ->setParameter('limite', $limite)
            ->orderBy('e.data_de_expiracao_plano', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encontra empresas por plano de pagamento.
     *
     * @return array<int, Empresa>
     */
    public function findByPlanoDePagamento(PlanoDePagamento|int $plano): array
    {
        $planoId = $plano instanceof PlanoDePagamento ? $plano->getId() : $plano;

        return $this->createQueryBuilder('e')
            ->where('e.plano_de_pagamento = :planoId')
            ->setParameter('planoId', $planoId)
            ->orderBy('e.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca empresas por termo de pesquisa no nome.
     *
     * @return array<int, Empresa>
     */
    public function findBySearchTerm(string $termo): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.nome LIKE :termo')
            ->setParameter('termo', '%' . trim($termo) . '%')
            ->orderBy('e.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retorna empresas cadastradas no período especificado.
     *
     * @return array<int, Empresa>
     */
    public function findByPeriodoCadastro(\DateTimeInterface $inicio, \DateTimeInterface $fim): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.created_at BETWEEN :inicio AND :fim')
            ->setParameter('inicio', $inicio)
            ->setParameter('fim', $fim)
            ->orderBy('e.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retorna um QueryBuilder básico para empresas que pode ser usado para construir consultas mais complexas.
     */
    public function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.nome', 'ASC');
    }

    /**
     * Salva uma empresa no banco de dados.
     */
    public function save(Empresa $empresa, bool $flush = true): void
    {
        $this->getEntityManager()->persist($empresa);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove uma empresa do banco de dados.
     */
    public function remove(Empresa $empresa, bool $flush = true): void
    {
        $this->getEntityManager()->remove($empresa);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
