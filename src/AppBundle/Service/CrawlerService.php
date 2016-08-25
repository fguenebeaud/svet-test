<?php

namespace AppBundle\Service;

use Goutte\Client;

/**
 * Class CrawlerService
 * @package AppBundle\Service
 */
class CrawlerService
{
    const CLIENT_NAME_LEBONCOIN = 'leboncoin';

    /**
     * Liste des sites configurés dans le yml
     *
     * @var array
     */
    private $scraping;

    /**
     * CrawlerService constructor.
     */
    public function __construct(array $scraping)
    {
        $this->scraping = $scraping;
    }

    /**
     * @param string $category
     * @param string $place
     * @param integer $numberResult
     * @param string $clientName
     * @param string $method
     * @param array|null $parameters
     */
    public function getAdverts($category, $place, $numberResult = 100, $clientName, $filter, $method='GET', array $parameters = null)
    {
        if (array_key_exists($clientName, $this->scraping)) {
            $clientArray = $this->scraping[$clientName];
            $baseUrl = $clientArray['url'];

            $clientUrl = str_replace('[CATEGORY]', $category, $baseUrl);
            $clientUrl = str_replace('[PLACE]', $place, $baseUrl);
          //  $clientUrl = str_replace('[PAGE]', $pageStart, $baseUrl);


            $client = new Client();

            $crawler = $client->request($method, $clientUrl);
        } else {
            throw new \Exception('La configuration pour '.$clientName.' n\'éxiste pas. Avez-configurer ce domaine ?');
        }
    }
}
