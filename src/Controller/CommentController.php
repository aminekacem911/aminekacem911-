<?php

namespace App\Controller;
use Doctrine\ORM\EntityManager;
use App\Entity\Comment;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use hmerritt\Imdb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class CommentController extends AbstractController
{
    
    
    /**
     * @Route("/comment/new", name="comment_new")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function new(Request $request,Session $session): Response
    {

        $userId = $this->getUser()->getEmail();

        $imdb = new Imdb;
        $res = $imdb->film("the lod of the rings");
            
            $comment = new Comment();

            $form = $request->request->all();

            if(isset($form['comment'])){
                    $comment->setComment($form['comment']);
            }
            if(isset($form['user'])){
                    $comment->setUser($userId);
            }
            if(isset($form['film'])){
                $comment->setFilm($form['film']);
        }
                     if(isset($form['approve'])){
                    $comment->setApprove(0);
             }
                       
                if (isset($form['comment'])) {
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($comment);
                        $entityManager->flush();
                        $referer = filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL);
                        return $this->redirect($referer);

                }
    
                return $this->render('comment/index.html.twig',compact('res'));
    }
     /**
     * @Route("/comment/approve", name="comment_approve")
     * @IsGranted("ROLE_ADMIN")
     *
     */
    public function approve()
    {      
    
        $comments = $this->getDoctrine()
        ->getRepository(Comment::class)
        ->findAllNonApp();
        //dd($products);
        return $this->render('navigation/commentAdmin.html.twig', compact('comments'));
    }
 /**
     * @Route("/com_app/{id}")
     * @IsGranted("ROLE_ADMIN")
     *
     */
    public function app(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
     
        $obj = $em->getRepository(Comment::class)->find($id);
        $obj->setApprove(1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($obj);
        $em->flush();
        $referer = filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL);
                        return $this->redirect($referer);
    }



      /**
     * @Route("/com_delete/{id}")
     * @IsGranted("ROLE_ADMIN")
     *
     */
    public function delete(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
     
        $obj = $em->getRepository(Comment::class)->find($id);
     
        $em->remove($obj);
        $em->flush();
        $referer = filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL);
                        return $this->redirect($referer);
    }
}
