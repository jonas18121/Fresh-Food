<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\SearchProductData;
use Doctrine\ORM\Query\Expr\Join;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    protected $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }

    /**
     * @return PaginationInterface
     */
    public function searchProduct(User $suserId, SearchProductData $productData): PaginationInterface
    {
        $query = $this->createQueryBuilder('p')
            ->select('c', 'e', 'p')
            ->join('p.classifiedIn', 'c')
            ->join('p.placeIn', 'e')
            ->where('p.author = :val')
            ->orderBy('p.id', 'DESC')
            ->setParameter('val', $suserId)
        ;

        if (!empty($productData->q)) {
            $query = $query
            ->andWhere('p.name LIKE :q')
            ->setParameter('q', "%{$productData->q}%");
        }

        if(!empty($productData->categories)) {
            $query = $query
            ->andWhere('c.id IN (:categories)')
            ->setParameter('categories', $productData->categories);
        }

        if(!empty($productData->emplacements)) {
            $query = $query
            ->andWhere('e.id IN (:emplacements)')
            ->setParameter('emplacements', $productData->emplacements);
        }

        $query = $query->getQuery();
        return $this->paginator->paginate(
            $query,
            $productData->page,
            12
        );
    }

     /**
     * @return Product[]
     */
    public function findWithCategories()
    {
        return $this->createQueryBuilder('p')
            ->leftjoin('p.classifiedIn', 'c')
            ->addSelect('c')
            ->orderBy('p.purchase_date', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[]
     */
    public function findWithCategoriesBis()
    {
        return $this->getEntityManager()->createQuery(
            'SELECT b, c FROM '.Product::class.' b '.
            'LEFT JOIN b.classifiedIn c '.
            'ORDER BY b.purchase_date DESC'
        )->setMaxResults(50)
        ->getResult();
    }

    /**
     * @return Product[]
     */
    public function findByClassifiedInOne(Category $category)
    {
        return $this->createQueryBuilder('b')
        ->join('b.classifiedIn', 'c',
        Join::WITH,'c = :category')
        ->orderBy('b.purchase_date','DESC')
        ->setMaxResults(50)
        ->getQuery()
        ->setParameter('category', $category)
        ->getResult();
    }
}
