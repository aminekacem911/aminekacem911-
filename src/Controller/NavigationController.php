<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\Session;
use SparksCoding\MovieInformation\MovieInformation;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/search")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function search(Request $request): Response
    {
        $form = $request->request->all();
        if (isset($form['search'])) {
            $imdb = new Imdb;
            $res = $imdb->film($form['search']);
           $title = $res['title'];
            #return $this->redirectToRoute('app_comment_data',array($title));
            return $this->redirectToRoute(
                'app_comment_data',
                array('key' => $title),
                Response::HTTP_MOVED_PERMANENTLY // = 301
            );
        }
            
    


        return $this->render('search/index.html.twig');
    }
   /**
     * @Route("/data")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function data(Request $request): Response
    {
        
        $key = $request->query->get('key');

        //dd($res);
         $imdb = new Imdb;
         $res = $imdb->film($key);
           
         $comments = $this->getDoctrine()
        ->getRepository(Comment::class)
        ->findspecApp($key);
        //dd($comments);
       

        return $this->render('search/data.html.twig',compact('res','comments'));
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