<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
//permet d'avoir accès à la requete HTTP du client
use Symfony\Component\HttpFoundation\Request;
//permet de faire appel aux options de génération de l'url
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
//permet l'encondage en Json et la défintion du Content-type
use Symfony\Component\HttpFoundation\JsonResponse;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
      //On ne sait pas combien de pages il y a, mais il faut que $page>1
      if($page < 1){
        //On déclenche une exception NotFoundHttpException qui affichera une 404
        throw new NotFoundHttpException('Page "' .$page.'" inexistante');
      }
      // Notre liste d'annonce en dur
      $listAdverts = array(
        array(
          'title'   => 'Recherche développpeur Symfony',
          'id'      => 1,
          'author'  => 'Alexandre',
          'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
          'date'    => new \Datetime()),
        array(
          'title'   => 'Mission de webmaster',
          'id'      => 2,
          'author'  => 'Hugo',
          'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
          'date'    => new \Datetime()),
        array(
          'title'   => 'Offre de stage webdesigner',
          'id'      => 3,
          'author'  => 'Mathieu',
          'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
          'date'    => new \Datetime())
      );
      //On veut avoir l'URL de l'annonce d'id 5
      $url = $this->generateUrl(
        'oc_platform_view',
        array('id' => 5),
        UrlGeneratorInterface::ABSOLUTE_URL
      );
      // $url vaut "/platform/advert/5"


      return $this->render('OCPlatformBundle:Advert:index.html.twig', array('listAdverts' => $listAdverts));
    }

    public function viewAction($id){
      $advert = array(
        'title'   => 'Recherche développpeur Symfony2',
        'id'      => $id,
        'author'  => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
        'date'    => new \Datetime()
      );

      return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
        'advert' => $advert
      ));
    }

    public function addAction(Request $request){

      //Si la requête est en POST, c'est que le client à soumis le formulaire
      if($request->isMethod('POST')){
        //ici on ajoutera l'annonce
        $request->getSession()->getFlashBag()->add('info', 'Annonce bien enregistrée');

        //Puis on redirige vers la page de visualisation de cette annonce
        return $this->redirectToRoute('oc_platform_view', array('id' => 5));
      }
      //Si on est pas en POST, on affiche le formuliare d'ajout
      return $this->render('OCPlatformBundle:Advert:add.html.twig');
    }

    public function editAction($id, Request $request){
      if($request->isMethod('POST')){
        $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

        return $this->redirectToRoute('oc_platform_view', array('id' => 5));
      }

      $advert = array(
        'title'   => 'Recherche développpeur Symfony',
        'id'      => $id,
        'author'  => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
        'date'    => new \Datetime()
      );


      //Si on est pas en POST, on affiche le formulaire d'édition
      return $this->render('OCPlatformBundle:Advert:edit.html.twig', array('advert' => $advert));
    }

    public function deleteAction(Request $request, $id){

      if ($request->isMethod('POST')) {
        $request->getSession()->getFlashBag()->add('notice', 'Annonce supprimée');

        return $this->redirectToRoute('oc_platform_home', array('page' => 1));
      }

      $advert = array(
        'title'   => 'Recherche développpeur Symfony',
        'id'      => $id,
        'author'  => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
        'date'    => new \Datetime()
      );

      return $this->render('OCPlatformBundle:Advert:delete.html.twig', array('advert' => $advert));
    }

    public function menuAction($limit){
      //On fixe arbitrairement une liste
      $listAdverts = array(
        array('id' => 2, 'title' => 'Recherche développeur Symfony'),
        array('id' => 5, 'title' => 'Mission de webmaster'),
        array('id' => 9, 'title' => 'Recherche stagiaire Symfony')
      );

      return $this->render('OCPlatformBundle:Advert:menu.html.twig',
                            array('listAdverts' => $listAdverts));
    }

    public function testAction($id){

      //VERSION LONGUE
      // $response = new Response(json_encode(array('id' => $id)));
      //
      // $response->headers->set('Content-type', 'application/json');
      //
      // return $response;

      //VERSION COURTE
      return new JsonResponse(array('id' => $id));

      // return $this->redirectToRoute('oc_platform_home');
    }

}
