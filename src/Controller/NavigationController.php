<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\Session;
use SparksCoding\MovieInformation\MovieInformation;
use hmerritt\Imdb;
class NavigationController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(Session $session)
    {
        $return = [];

                if($session->has('message'))
                {
                        $message = $session->get('message');
                        $session->remove('message'); //on vide la variable message dans la session
                        $return['message'] = $message; //on ajoute à l'array de paramètres notre message
                }
                return $this->render('navigation/home.html.twig', $return);
    }

        /**
         * @Route("/membre", name="membre")
         */
    public function membre(Session $session)
        {
                $return = [];
               // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
                if($session->has('message'))
                {
                        $message = $session->get('message');
                        $session->remove('message'); //on vide la variable message dans la session
                        $return['message'] = $message; //on ajoute à l'array de paramètres notre message
                }
                return $this->render('navigation/membre.html.twig', $return);
        }
         /**
         * @Route("/movies", name="movies")
         */
    public function movies(Session $session)
    {
       // $movie = new MovieInformation('The Matrix', array('plot'=>'full', 'tomatoes'=>'true'));
        //$title = $movie->title; 
        $imdb = new Imdb;
        $res = $imdb->film("the lod of the rings");
            return $this->render('navigation/movies.html.twig',compact('res'));
    }

        /**
         * @Route("/admin", name="admin")
         */
        public function admin(Session $session)
        {
                $utilisateur = $this->getUser();
                if(!$utilisateur)
                {
                        $session->set("message", "Merci de vous connecter");
                        return $this->redirectToRoute('app_login');
                }

                if (in_array('ROLE_ADMIN', $utilisateur->getRoles())){
                        return $this->render('navigation/admin.html.twig');
                }
              
                //return $this->redirectToRoute('/membre', $return);

        }

}