<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

abstract class DefaultRepository extends EntityRepository
{
    const SORT_DESC = 'DESC';

    const SORT_ASC = 'ASC';

    /**
     * Recherche par terme
     *
     * @param string $field
     * @param string $prefix
     * @param string $term
     * @param string $sort
     * @param string $orderBy
     * @return array
     */
    public function findByTerms($prefix, $field, $term, $sort, $orderBy)
    {

        $qb = $this->createQueryBuilder($prefix)
            ->where($prefix.'.'.$field.' LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->orderBy($prefix.'.'.$sort, $orderBy)
        ;

        return $qb->getQuery()->getResult();
    }
}
