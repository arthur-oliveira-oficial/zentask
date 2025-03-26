<?php

namespace App\Repository\painel_admin;

use App\Entity\painel_admin\Administrador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Administrador>
 *
 * @method Administrador|null find($id, $lockMode = null, $lockVersion = null)
 * @method Administrador|null findOneBy(array $criteria, array $orderBy = null)
 * @method Administrador[]    findAll()
 * @method Administrador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Administrador_Repository extends ServiceEntityRepository
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
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Encontra administradores pelo status
     */
    public function findByStatus(string $status): array
    {
        return $this->findBy(['status' => $status]);
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
     */
    public function findAtivos(): array
    {
        return $this->findByStatus('ativo');
    }

    /**
     * Encontra administradores que nunca fizeram login
     */
    public function findSemLogin(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.ultimo_login_em IS NULL')
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
