<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;

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
      $em = $this->getDoctrine()->getManager();
      //récupération du repo
      $repository = $em->getRepository('OCPlatformBundle:Advert');

      $advert = $repository->find($id);

      if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id " . $id . "n'existe pas.");
      }

      //on récupère la liste des Applications de cette annonce
      $listApplications = $em->getRepository('OCPlatformBundle:Application')
                            ->findBy(array('advert' => $advert));

      //on récupère les compétences requises liées à l'annonce
      $listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')
                            ->findBy(array('advert' => $advert));

      //nul besoin d'aller récupérer les catégories, doctrine les a injectées
      //dans $advert lors de l'appel de la méthode find sur le repo

      return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
        'advert' => $advert,
        'listApplications' => $listApplications,
        'listAdvertSkills' => $listAdvertSkills
      ));
    }

    public function addAction(Request $request){

      //on récupère l'Entity Manager
      $em = $this->getDoctrine()->getManager();

      //Si la requête est en POST, c'est que le client à soumis le formulaire
      if($request->isMethod('POST')){
        //ici on ajoutera l'annonce
        //ON récupère le service d'Antispam
        $antispam = $this->container->get('oc_platform.antispam');

        //on test le text du client
        $text = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
        if ($antispam->isSpam($text)) {
          throw new \Exception('Votre message a été détécté comme spam');
        //message de succès à transmettre
        $request->getSession()->getFlashBag()->add('info', 'Annonce bien enregistrée');
        }
        //Puis on redirige vers la page de visualisation de cette annonce
        return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
      }

      //Création de l'entité
      $advert = new Advert();
      $advert->setTitle('Recherche le sens de la vie')
            ->setAuthor('Dieu')
            ->setContent('Apparement ce serait 42, mais des recherches sont toujours en cours');
      //Création de l'image
      $image = new Image();
      $image->setUrl('https://www.the-scientist.fr/wp-content/uploads/2016/08/the-hitchhikers-guide-to-the-galaxy-everything-42-1024x768-wallpaper_www.wallpaperhi.com_73.jpg')
        ->setAlt('Le sens de la vie');
      //on lie l'image à l'annonce

      //création d'une candidature
      $application1 = new Application();
      $application1->setAuthor('BobLeponge')
        ->setContent('Il y aura du paté de crabe?');

      //création d'une autre candidature
      $application2 = new Application();
      $application2->setAuthor('Beethoveen')
        ->setContent('Hein?');

      //on récupère toutes les compétences
      $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();

      //Pour chaque compétence
      foreach ($listSkills as $skill) {
        $advertSkill = new AdvertSkill();

        //on établit les relations
        $advertSkill->setSkill($skill);
        $advertSkill->setAdvert($advert);

        //on définit chaque compétence requise au niveau 'Expert'
        $advertSkill->setLevel('Expert');

        //on persiste la relation
        $em->persist($advertSkill);
      }

      //on récupère les catégories
      $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

      //on assigne toutes les catégories à l'annonce
      foreach ($listCategories as $category) {
        $advert->addCategory($category);
      }

      //on lie les éléments
      $application1->setAdvert($advert);
      $application2->setAdvert($advert);

      $advert->setImage($image);

      //1: on "persiste" l'entité
      $em->persist($advert);
      $em->persist($application1);
      $em->persist($application2);

      //2: on "flush" tout ce qui a été persisté avant (enregistrement en BDD)
      $em->flush();

      //Si on est pas en POST, on affiche le formuliare d'ajout
      return $this->render('OCPlatformBundle:Advert:add.html.twig', array('advert' => $advert));
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

      $em = $this->getDoctrine()->getManager();

      if ($request->isMethod('POST')) {
        $request->getSession()->getFlashBag()->add('notice', 'Annonce supprimée');

        return $this->redirectToRoute('oc_platform_home', array('page' => 1));
      }

      $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

      if (null === $advert) {
        throw new NotFoundHttpException("Pas d'annonce trouvé à l'id " . $id);
      }

      foreach ($advert->getCategories() as $category) {
        $advert->removeCategory($category);
      }

      /**
       * pour le moment les contraintes de clés étrangère
       *de la table oc_application empêchent la suppresion de l'annonce
       */
      // $em->remove($advert);

      $em->flush();

      return $this->render('OCPlatformBundle:Advert:delete.html.twig', array('advert' => $advert));
    }

    public function editImageAction(Request $request, $advertId){

      $em = $this->getDoctrine()->getManager();

      //On récupère l'annonce
      $advert = $em->getRepository('OCPlatformBundle:Advert')->find($advertId);

      //on modifie l'url de l'Image
      $advert->getImage()->setUrl('https://pbs.twimg.com/profile_images/1058085082/42_fullcolor_20040622.png');

      //pas besoin de persister car ces objets sont récupérés depuis Doctrine
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'l\'image a bien été modifiée');

      return $this->render('OCPlatformBundle:Advert:edit.html.twig', array('advert' => $advert));
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
      return $this->render('OCPlatformBundle:Advert:test.html.twig');

      // return $this->redirectToRoute('oc_platform_home');
    }

}
