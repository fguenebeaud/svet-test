<?php

namespace AppBundle\Repository;

/**
 * Class AdvertRepository
 * @package AppBundle\Repository
 */
class AdvertRepository extends DefaultRepository
{
    /**
     * Table prefix
     */
    const PREFIX = 'advert';

    /**
     * Recherche d'une annonce en fonction de son nom
     * 
     * @param string $field
     * @param string $title
     * @param string $sort
     * @param string $orderBy
     * 
     * @return array
     */
    public function findByTitle($field = 'title', $title = '', $sort = "price", $orderBy = "DESC")
    {
        return parent::findByTerms(self::PREFIX, $field, $title, $sort, $orderBy);
    }
}
