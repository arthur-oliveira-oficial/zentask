<?php

namespace App\Repository\painel_admin;

use App\Entity\painel_admin\PlanoDePagamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanoDePagamento>
 *
 * @method PlanoDePagamento|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanoDePagamento|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanoDePagamento[]    findAll()
 * @method PlanoDePagamento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanoDePagamento_Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanoDePagamento::class);
    }

    /**
     * Encontra um plano de pagamento pelo nome
     */
    public function findByNome(string $nome): ?PlanoDePagamento
    {
        return $this->findOneBy(['nome' => $nome]);
    }

    /**
     * Encontra planos de pagamento pela periodicidade
     */
    public function findByPeriodicidade(string $periodicidade): array
    {
        return $this->findBy(['periodicidade' => $periodicidade]);
    }

    /**
     * Encontra planos de pagamento por faixa de preço
     */
    public function findByFaixaPreco(float $precoMinimo, float $precoMaximo): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.preco >= :precoMinimo')
            ->andWhere('p.preco <= :precoMaximo')
            ->setParameter('precoMinimo', $precoMinimo)
            ->setParameter('precoMaximo', $precoMaximo)
            ->orderBy('p.preco', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encontra planos de pagamento ordenados por preço (crescente)
     */
    public function findAllOrderedByPrecoAsc(): array
    {
        return $this->findBy([], ['preco' => 'ASC']);
    }

    /**
     * Encontra planos de pagamento ordenados por preço (decrescente)
     */
    public function findAllOrderedByPrecoDesc(): array
    {
        return $this->findBy([], ['preco' => 'DESC']);
    }

    /**
     * Verifica se existe um plano de pagamento com o nome informado
     */
    public function nomeExists(string $nome): bool
    {
        return null !== $this->findByNome($nome);
    }

    /**
     * Busca planos que contenham determinado recurso no texto
     */
    public function findByRecurso(string $recurso): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.recursos LIKE :recurso')
            ->setParameter('recurso', '%' . $recurso . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Salva um plano de pagamento no banco de dados
     */
    public function save(PlanoDePagamento $planoDePagamento, bool $flush = true): void
    {
        $this->getEntityManager()->persist($planoDePagamento);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove um plano de pagamento do banco de dados
     */
    public function remove(PlanoDePagamento $planoDePagamento, bool $flush = true): void
    {
        $this->getEntityManager()->remove($planoDePagamento);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
