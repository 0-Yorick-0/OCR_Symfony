<?php

// src/OCCoreBundle/Controller/HomeController.php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function indexAction()
    {
      //ici on fera une requête en BDD pour récupérer les 3 dernières annonces
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

        return $this->render('OCCoreBundle:Home:index.html.twig', array('listAdverts' => $listAdverts));
    }

    public function contactAction(Request $request){

      if($request->isMethod('POST')){
        /**
         * Ici on fera les contrôles nécessaires à la validation du formulaire
         *puis on redirigera vers la home
         */

        $request->getSession()->getFlashBag()->add('notice', 'Le formulaire de contact a bien été validé');

        return $this->redirectToRoute('oc_core_homepage');
      }

      //Pour le moment on redirige d'office vers la home avec un flashbag

      $request->getSession()->getFlashBag()->add('notice', 'La page de contact n\'est pas encore disponible, merci de revenir plus tard');

      return $this->redirectToRoute('oc_core_homepage');

      //Plus tard on retournera cette réponse
      // return $this->render('OCCoreBundle:Home:contactForm.html.twig');


    }
}
