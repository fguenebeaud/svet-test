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
     * @var array $scrapping
     */
    private $scraping;

    /**
     * CrawlerService constructor.
     *
     * @param array $scraping Tableau de données
     */
    public function __construct(array $scraping)
    {
        $this->scraping = $scraping;
    }

    /**
     * Récupère les annonces
     *
     * @param string $category     Catégorie de recherche
     * @param string $place        Lieu de recherche
     * @param string $clientName   Nom du client
     * @param int    $numberResult Nombre de résultat max
     * @param string $method       Méthode de requête
     *
     * @return array
     * @throws \Exception
     */
    public function getAdverts(
        $category,
        $place,
        $clientName,
        $numberResult = 100,
        $method = 'GET'
    ) {
        if (array_key_exists($clientName, $this->scraping)) {
            $clientArray = $this->scraping[$clientName];
            $baseUrl = $clientArray['url'];

            $clientUrl = str_replace('[CATEGORY]', $category, $baseUrl);
            $clientUrl = str_replace('[PLACE]', $place, $clientUrl);
            $filters = $clientArray['filters'];
            $client = new Client();
            $cpt = 0;
            $adverts = [];
            $pageStart = $clientArray['page_start'];

            while ($numberResult > $cpt) {
                $clientUrlPage = str_replace('[PAGE]', $pageStart, $clientUrl);

                //Appel de la requête
                $crawler = $client->request($method, $clientUrlPage);

                $content = $crawler->html();

                if (strpos(
                    $content,
                    $clientArray['filters']['filter_advert_default']
                )) {
                    // Filtrage du contenu html, en ne récupérant
                    // que les sections (annonces) en
                    // vérifidant auparavant si la classe existe
                    $crawler->filter($filters['filter_advert'])->each(
                        function (Crawler $node) use ($filters, &$cpt, $numberResult, &$adverts) {
                            if ($numberResult > $cpt) {
                                $advert = [
                                    'title' => trim($node->filter($filters['filter_title'])->text()),
                                    'place' => trim($node->filter($filters['filter_place'])->eq(1)->text()),
                                    'link'  => $filters['add_protocole'] ?  $filters['protocole'].$node->filter($filters['filter_url'])->attr('href') : $node->filter($filters['filter_url'])->attr('href'),
                                    'price' => null,
                                    'index' => $cpt,
                                ];

                                // Récupération du prix qui peut ne pas exister,
                                // on vérifie donc si la classe existe
                                // dans le contenu HTML
                                $itemContent = $node->html();
                                if (strpos($itemContent, $filters['filter_price_default'])) {
                                    $advert['price'] = intval($node->filter($filters['filter_price'])->text());
                                } else {
                                }
                                $cpt++;

                                $adverts[] = $advert;
                            }
                        }
                    );
                    $pageStart++;
                } else {
                    break;
                }
            }

            return $adverts;
        } else {
            throw new \Exception(
                'La configuration pour '.$clientName.' n\'éxiste pas. Avez-configurer ce domaine ?'
            );
        }
    }
}
