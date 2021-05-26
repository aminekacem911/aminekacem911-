<?php

namespace App\Controller;

use hmerritt\Imdb;
use App\Entity\Comment;
use App\Entity\Setting;
use App\Entity\Utilisateur;
use App\Repository\SettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SparksCoding\MovieInformation\MovieInformation;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\FaqRepository;

class NavigationController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(SettingRepository $rep, FaqRepository $faqRepository)
    {
        $faqs = $faqRepository->findAll();
        $setting =$rep->find(1);
        //  dd($setting);
        return $this->render('navigation/home.html.twig', compact('setting', 'faqs'));
    }

    /**
     * @Route("/search")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function search(Request $request, SettingRepository $rep, ): Response
    {
        $setting =$rep->find(1);
        $form = $request->request->all();
        if (isset($form['search'])) {
            $imdb = new Imdb;
            $res = $imdb->film($form['search']);
            $title = $res['title'];
            #return $this->redirectToRoute('app_comment_data',array($title));
            return $this->redirectToRoute(
                'app_navigation_data',
                array('key' => $title),
                Response::HTTP_MOVED_PERMANENTLY // = 301
            );
        }
            
    


        return $this->render('search/index.html.twig', compact('setting'));
    }
    /**
      * @Route("/data")
      * @IsGranted("IS_AUTHENTICATED_FULLY")
      */
    public function data(Request $request, SettingRepository $rep, ): Response
    {
        $setting =$rep->find(1);
        $key = $request->query->get('key');

        //dd($res);
        $imdb = new Imdb;
        $res = $imdb->film($key);
           
        $comments = $this->getDoctrine()
        ->getRepository(Comment::class)
        ->findspecApp($key);
        
       

        return $this->render('search/data.html.twig', compact('res', 'comments', 'setting'));
    }
   
    /**
     * @Route("/admin", name="admin")
     */
    public function admin(Session $session)
    {
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            $session->set("message", "Merci de vous connecter");
            return $this->redirectToRoute('app_login');
        }

        if (in_array('ROLE_ADMIN', $utilisateur->getRoles())) {
            $em = $this->getDoctrine()->getManager();
            $repComment = $em->getRepository(Comment::class);
            $countC = $repComment->createQueryBuilder('a')
                   
                    ->select('count(a.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
            $repUsers = $em->getRepository(Utilisateur::class);
            $countU = $repUsers->createQueryBuilder('u')
                   
                    ->select('count(u.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
                   
            return $this->render('navigation/admin.html.twig', compact('countC', 'countU'));
        }
    }
}
