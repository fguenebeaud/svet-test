<?php

namespace AppBundle\Service;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

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

            $client = new Client();

            $cptAdvert = 0;
            $pageStart = $clientArray['page_start'];

            while (100 >= $cptAdvert) {
                $clientUrl = str_replace('[PAGE]', $pageStart, $baseUrl);

                //Appel de la requête
                $crawler = $client->request($method, $clientUrl);
                // Filtrage du contenu html, en ne récupérant que les sections (annonces)
                $crawler->filter('section.item_infos')->each(function (Crawler $node) {
                    
                    $itemContent = $node->html();
                    if (strpos($itemContent, 'item_price')) {
                        echo $node->filter('.item_price')->text();
                    } else {
                        echo 'none';
                    }
                });
            }
            $crawler = $client->request($method, $clientUrl);
        } else {
            throw new \Exception('La configuration pour '.$clientName.' n\'éxiste pas. Avez-configurer ce domaine ?');
        }
    }
}
