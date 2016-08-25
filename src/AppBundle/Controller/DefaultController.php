<?php

namespace AppBundle\Controller;

use Goutte\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Route("/result", name="result")
     */
    public function resultAction()
    {
        // TODO Mettre dans un service à part qui prend en paramètre lieu, catégorie, numéro de page ( par défaut 1 ), le filtre correspondant
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.leboncoin.fr/animaux/offres/rhone_alpes/?o=1');
        $crawler->filter('section.item_infos')->each(function (Crawler $node) {
           $itemContent = $node->html();
           if (strpos($itemContent, 'item_price')) {
               echo $node->filter('.item_price')->text();
           } else {
               echo 'none';
           }
        });exit;
        echo $crawler->html();exit;
    }
}
