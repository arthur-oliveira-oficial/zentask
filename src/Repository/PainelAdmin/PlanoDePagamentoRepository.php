<?php

declare(strict_types=1);

namespace App\Repository\PainelAdmin;

use App\Entity\PainelAdmin\PlanoDePagamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositório para gerenciar operações relacionadas aos planos de pagamento no banco de dados.
 *
 * @extends ServiceEntityRepository<PlanoDePagamento>
 *
 * @method PlanoDePagamento|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanoDePagamento|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanoDePagamento[]    findAll()
 * @method PlanoDePagamento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanoDePagamentoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanoDePagamento::class);
    }

    /**
     * Encontra um plano de pagamento pelo nome.
     */
    public function findByNome(string $nome): ?PlanoDePagamento
    {
        return $this->findOneBy(['nome' => trim($nome)]);
    }

    /**
     * Verifica se existe um plano de pagamento com o nome informado.
     */
    public function nomeExists(string $nome): bool
    {
        return null !== $this->findByNome(trim($nome));
    }

    /**
     * Encontra planos de pagamento pela periodicidade.
     *
     * @param string $periodicidade Uma das constantes PERIODICIDADE_* da classe PlanoDePagamento
     * @return array<int, PlanoDePagamento>
     */
    public function findByPeriodicidade(string $periodicidade): array
    {
        return $this->findBy(['periodicidade' => $periodicidade], ['nome' => 'ASC']);
    }

    /**
     * Encontra planos de pagamento por faixa de preço.
     *
     * @return array<int, PlanoDePagamento>
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
     * Encontra planos de pagamento ordenados por preço (crescente).
     *
     * @return array<int, PlanoDePagamento>
     */
    public function findAllOrderedByPrecoAsc(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.preco', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encontra planos de pagamento ordenados por preço (decrescente).
     *
     * @return array<int, PlanoDePagamento>
     */
    public function findAllOrderedByPrecoDesc(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.preco', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca planos que contenham determinado recurso no texto.
     *
     * @return array<int, PlanoDePagamento>
     */
    public function findByRecurso(string $recurso): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.recursos LIKE :recurso')
            ->setParameter('recurso', '%' . trim($recurso) . '%')
            ->orderBy('p.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca planos criados em um intervalo de datas.
     *
     * @return array<int, PlanoDePagamento>
     */
    public function findByPeriodoCriacao(\DateTimeInterface $inicio, \DateTimeInterface $fim): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.created_at BETWEEN :inicio AND :fim')
            ->setParameter('inicio', $inicio)
            ->setParameter('fim', $fim)
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retorna o plano mais barato disponível.
     */
    public function findMaisBarato(): ?PlanoDePagamento
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.preco', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Retorna o plano mais caro disponível.
     */
    public function findMaisCaro(): ?PlanoDePagamento
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.preco', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Retorna um QueryBuilder base para consultas personalizadas.
     */
    public function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.nome', 'ASC');
    }

    /**
     * Salva um plano de pagamento no banco de dados.
     */
    public function save(PlanoDePagamento $planoDePagamento, bool $flush = true): void
    {
        $this->getEntityManager()->persist($planoDePagamento);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove um plano de pagamento do banco de dados.
     */
    public function remove(PlanoDePagamento $planoDePagamento, bool $flush = true): void
    {
        $this->getEntityManager()->remove($planoDePagamento);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
