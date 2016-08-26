<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Advert;
use AppBundle\Service\CrawlerService;
use AppBundle\Type\AdvertType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdvertController
 * @package AppBundle\Controller
 */
class AdvertController extends Controller
{
    /**
     * @Route("/advert/new", name="advert_new")
     * @Method({"GET"})
     * @return Response
     */
    public function newAction()
    {
        $entity = new Advert();
        $form   = $this->createCreateForm($entity);

        return $this->render('@App/Advert/new.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/advert/create", name="advert_create")
     * @Method({"PUT"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Advert();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'annonce a été créée avec succès.'
            );

            return $this->redirect($this->generateUrl('advert'));
        }

        return $this->render('@App/Advert/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route(
     *     "/advert/scraping/{client}/{category}/{place}/{numberResult}",
     *     requirements={"numberResult" = "\d+"},
     *     name="scraping",
     *     defaults={
     *       "client" = "leboncoin",
     *       "category" = "animaux",
     *       "place" = "rhone_alpes",
     *       "numberResult" = "100"
     *     }
     * )
     * @Method({"GET"})
     *
     * @param string  $client
     * @param string  $category
     * @param string  $place
     * @param integer $numberResult
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function scrapingAction($client, $category, $place, $numberResult)
    {
        $results = $this->get('app.crawler.service')->getAdverts($category, $place, $client, $numberResult);

        return $this->render('@App/Advert/scraping.html.twig', array(
            'results' => $results,
        ));
    }

    /**
     * Enregistrer la liste dans la base
     *
     * @Route(
     *     "/advert/scraping_insert/{client}/{category}/{place}/{numberResult}",
     *     requirements={"numberResult" = "\d+"},
     *     name="scraping_insert",
     *     defaults={
     *       "client" = "leboncoin",
     *       "category" = "animaux",
     *       "place" = "rhone_alpes",
     *       "numberResult" = "100"
     *     }
     * )
     * @Method({"GET"})
     *
     * @param string  $client
     * @param string  $category
     * @param string  $place
     * @param integer $numberResult
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function scrapingInsertAction($client, $category, $place, $numberResult)
    {
        $em = $this->getDoctrine()->getManager();

        $results = $this->get('app.crawler.service')->getAdverts($category, $place, $client, $numberResult);

        foreach ($results as $result) {
            $advert = new Advert();
            $advert->setLink($result['link']);
            $advert->setPlace($result['place']);
            $advert->setTitle($result['title']);

            if ($result['price']) {
                $advert->setPrice($result['price']);
            }

            $em->persist($advert);
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            count($results).' annonces ont été enregistrées avec succès'
        );

        return $this->redirect($this->generateUrl('scraping', [
            'client'       => CrawlerService::CLIENT_NAME_LEBONCOIN,
            'place'        => 'rhone_alpes',
            'category'     => 'animaux',
            'numberResult' => 100,
        ]));
    }

    /**
     * @Route("/advert", name="advert")
     * @return Response
     */
    public function indexAction()
    {
        $adverts = $this->getDoctrine()->getRepository('AppBundle:Advert')->findAll();

        return $this->render('@App/Advert/index.html.twig', array(
            'adverts' => $adverts,
        ));
    }

    /**
     * @Route("/advert/delete/{id}", requirements={"id" = "\d+"}, name="advert_delete")
     * @return RedirectResponse
     * @param integer $id
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('AppBundle:Advert')->find($id);

        $em->remove($advert);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'annonce a été supprimée avec succès.'
        );

        return $this->redirect($this->generateUrl('advert'));
    }

    /**
     * @Route("/advert/edit/{id}", requirements={"id" = "\d+"}, name="advert_edit")
     * @Method({"PUT","POST","GET"})
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('AppBundle:Advert')->find($id);
        if (!($advert instanceof Advert)) {
            throw $this->createNotFoundException('Unable to find Advert entity.');
        }

        $editForm = $this->createEditForm($advert);

        if ($request->getMethod() == 'POST') {
            $editForm->handleRequest($request);
            if ($editForm->isValid()) {
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'L\'annonce a été modifiée avec succès.'
                );

                return $this->redirect($this->generateUrl('advert'));
            }
        }

        return $this->render('@App/Advert/edit.html.twig', array(
            'edit_form' => $editForm->createView(),
            'advert'      => $advert,
        ));
    }

    /**
     * @param Advert $advert
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(Advert $advert)
    {
        $form = $this->createForm(new AdvertType(), $advert, [
            'action' => $this->generateUrl('advert_create'),
            'method' => 'PUT',
        ]);

        $form->add(
            'submit',
            'submit',
            array('label' => 'Valider', 'attr' => ['class' => 'btn btn-sm btn-success'])
        );

        return $form;
    }

    /**
     * @param Advert $advert
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(Advert $advert)
    {
        $form = $this->createForm(new AdvertType(), $advert, array(
            'action' => $this->generateUrl('advert_edit', array('id' => $advert->getId())),
            'method' => 'POST',
        ));

        $form->add(
            'submit',
            'submit',
            array('label' => 'Valider', 'attr' => ['class' => 'btn btn-sm btn-success'])
        );

        return $form;
    }
}
