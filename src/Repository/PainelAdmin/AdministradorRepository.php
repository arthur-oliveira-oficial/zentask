<?php

declare(strict_types=1);

namespace App\Repository\PainelAdmin;

use App\Entity\PainelAdmin\Administrador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Administrador>
 */
class AdministradorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Administrador::class);
    }

    /**
     * Encontra um administrador pelo email
     */
    public function findByEmail(string $email): ?Administrador
    {
        try {
            return $this->createQueryBuilder('a')
                ->where('a.email = :email')
                ->setParameter('email', strtolower(trim($email)))
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    /**
     * Encontra administradores pelo status
     *
     * @return array<Administrador>
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', $status)
            ->orderBy('a.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Verifica se existe um administrador com o email informado
     */
    public function emailExists(string $email): bool
    {
        return null !== $this->findByEmail($email);
    }

    /**
     * Encontra administradores ativos
     *
     * @return array<Administrador>
     */
    public function findAtivos(): array
    {
        return $this->findByStatus(Administrador::STATUS_ATIVO);
    }

    /**
     * Encontra administradores pendentes
     *
     * @return array<Administrador>
     */
    public function findPendentes(): array
    {
        return $this->findByStatus(Administrador::STATUS_PENDENTE);
    }

    /**
     * Encontra administradores bloqueados
     *
     * @return array<Administrador>
     */
    public function findBloqueados(): array
    {
        return $this->findByStatus(Administrador::STATUS_BLOQUEADO);
    }

    /**
     * Encontra administradores que nunca fizeram login
     *
     * @return array<Administrador>
     */
    public function findSemLogin(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.ultimo_login_em IS NULL')
            ->orderBy('a.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encontra administradores criados no período especificado
     *
     * @return array<Administrador>
     */
    public function findByCriadosEm(\DateTimeInterface $inicio, \DateTimeInterface $fim): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.created_at BETWEEN :inicio AND :fim')
            ->setParameter('inicio', $inicio)
            ->setParameter('fim', $fim)
            ->orderBy('a.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Salva um administrador no banco de dados
     */
    public function save(Administrador $administrador, bool $flush = true): void
    {
        $this->getEntityManager()->persist($administrador);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove um administrador do banco de dados
     */
    public function remove(Administrador $administrador, bool $flush = true): void
    {
        $this->getEntityManager()->remove($administrador);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
